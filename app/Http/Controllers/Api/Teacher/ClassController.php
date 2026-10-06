<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSchedule;
use App\Models\ClassSubject;
use App\Models\PeriodSetting;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    use ApiResponse;

    public function myClasses(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $classIds = ClassSubject::where('teacher_id', $teacher->id)
            ->pluck('class_id')
            ->unique();

        $classes = SchoolClass::whereIn('id', $classIds)
            ->withCount('students')
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'id'             => $c->id,
                'name'           => $c->name,
                'students_count' => $c->students_count,
                'is_homeroom'    => $c->homeroom_teacher_id === $teacher->id,
                'schedule_image' => $c->schedule_image
                    ? asset('assets/uploads/schedules/' . $c->schedule_image)
                    : null,
            ]);

        return $this->success($classes);
    }

    // GET /classes/{class}/subjects — subjects this teacher teaches in this specific class
    public function subjects(Request $request, SchoolClass $class): JsonResponse
    {
        $teacher = $request->user();

        $subjects = ClassSubject::where('teacher_id', $teacher->id)
            ->where('class_id', $class->id)
            ->with('subject')
            ->get()
            ->map(fn (ClassSubject $cs) => ['id' => $cs->subject?->id, 'name' => $cs->subject?->name])
            ->filter(fn ($s) => $s['id'] !== null)
            ->values();

        if ($subjects->isEmpty()) {
            return $this->error('غير مصرح بالوصول لهذا الصف.', 403);
        }

        return $this->success($subjects);
    }

    // GET /classes/{class}/day-schedule?date=YYYY-MM-DD
    // This class's periods on the weekday that `date` falls on - meant to be
    // shown as a reminder banner on the daily-plan entry screen ("tomorrow
    // you have Religion period 1, Arabic period 2, ... for this class").
    public function daySchedule(Request $request, SchoolClass $class): JsonResponse
    {
        $teacher = $request->user();

        if (! $this->teachesClass($teacher, $class)) {
            return $this->error('غير مصرح بالوصول لهذا الصف.', 403);
        }

        $request->validate(['date' => ['required', 'date']]);

        $day = Carbon::parse($request->date)->dayOfWeek; // 0=Sun..6=Sat

        if ($day > 4) {
            return $this->success([]); // Friday/Saturday - no school
        }

        $periods = PeriodSetting::orderBy('period_number')->get();

        $slots = ClassSchedule::where('class_id', $class->id)
            ->where('day', $day)
            ->with(['subject', 'teacher'])
            ->orderBy('period_number')
            ->get()
            ->map(function ($slot) use ($periods) {
                $period = $periods->firstWhere('period_number', $slot->period_number);

                return [
                    'period_number' => $slot->period_number,
                    'label'         => $period?->label ?? ('الحصة ' . $slot->period_number),
                    'start_time'    => $period ? Carbon::parse($period->start_time)->format('H:i') : null,
                    'end_time'      => $period ? Carbon::parse($period->end_time)->format('H:i') : null,
                    'subject'       => $slot->subject ? ['id' => $slot->subject->id, 'name' => $slot->subject->name] : null,
                    'teacher'       => $slot->teacher ? ['id' => $slot->teacher->id, 'name' => $slot->teacher->name] : null,
                ];
            })
            ->values();

        return $this->success($slots);
    }

    private function teachesClass(Teacher $teacher, SchoolClass $class): bool
    {
        return $class->homeroom_teacher_id === $teacher->id
            || ClassSubject::where('class_id', $class->id)->where('teacher_id', $teacher->id)->exists();
    }

    public function students(Request $request, SchoolClass $class): JsonResponse
    {
        $teacher = $request->user();

        // Verify the teacher teaches in this class
        $teaches = ClassSubject::where('class_id', $class->id)
            ->where('teacher_id', $teacher->id)
            ->exists();

        if (! $teaches && $class->homeroom_teacher_id !== $teacher->id) {
            return $this->error('غير مصرح بالوصول لهذا الصف.', 403);
        }

        $students = Student::where('class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'national_id', 'phone', 'avatar', 'gender']);

        return $this->success([
            'class'    => ['id' => $class->id, 'name' => $class->name],
            'students' => $students->map(fn ($s) => [
                'id'          => $s->id,
                'name'        => $s->name,
                'national_id' => $s->national_id,
                'phone'       => $s->phone,
                'gender'      => $s->gender,
                'avatar'      => $s->avatar
                    ? asset('assets/uploads/students/' . $s->avatar)
                    : null,
            ]),
        ]);
    }
}
