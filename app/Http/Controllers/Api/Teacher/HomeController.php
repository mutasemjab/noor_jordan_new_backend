<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\ClassSubject;
use App\Models\PeriodSetting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        // All class IDs this teacher teaches in
        $classIds = ClassSubject::where('teacher_id', $teacher->id)
            ->pluck('class_id')
            ->unique()
            ->values();

        $totalStudents = Student::whereIn('class_id', $classIds)->where('is_active', true)->count();

        // Today's schedule (Jordan: Sun=0 … Thu=4)
        $today = Carbon::now()->dayOfWeek; // 0=Sun,1=Mon,...,6=Sat
        $todayNum = $today <= 4 ? $today : null; // only school days

        $periods = PeriodSetting::orderBy('period_number')->get();

        $todaySlots = $todayNum !== null
            ? ClassSchedule::where('teacher_id', $teacher->id)
                ->where('day', $todayNum)
                ->with(['schoolClass:id,name', 'subject:id,name_ar,color_class'])
                ->orderBy('period_number')
                ->get()
            : collect();

        $todaySchedule = $todaySlots->map(function ($slot) use ($periods) {
            $period = $periods->firstWhere('period_number', $slot->period_number);
            return [
                'period_number' => $slot->period_number,
                'label'         => $period?->label ?? ('الحصة ' . $slot->period_number),
                'start_time'    => $period ? Carbon::parse($period->start_time)->format('H:i') : null,
                'end_time'      => $period ? Carbon::parse($period->end_time)->format('H:i') : null,
                'class'         => $slot->schoolClass ? ['id' => $slot->schoolClass->id, 'name' => $slot->schoolClass->name] : null,
                'subject'       => $slot->subject ? ['id' => $slot->subject->id, 'name' => $slot->subject->name_ar, 'color' => $slot->subject->color_class] : null,
            ];
        })->values();

        // How many of today's classes still need attendance recorded
        $pendingAttendance = 0;
        if ($todayNum !== null && $todaySlots->isNotEmpty()) {
            $recordedClassIds = Attendance::whereDate('date', Carbon::today())
                ->whereIn('class_id', $classIds)
                ->distinct('class_id')
                ->pluck('class_id')
                ->unique();

            $pendingAttendance = $todaySlots
                ->pluck('class_id')
                ->unique()
                ->diff($recordedClassIds)
                ->count();
        }

        return $this->success([
            'teacher'           => [
                'id'     => $teacher->id,
                'name'   => $teacher->name,
                'avatar' => $teacher->avatar ? asset('assets/uploads/teachers/' . $teacher->avatar) : null,
            ],
            'stats' => [
                'classes_count'      => $classIds->count(),
                'total_students'     => $totalStudents,
                'today_periods'      => $todaySlots->count(),
                'pending_attendance' => $pendingAttendance,
            ],
            'today_schedule' => $todaySchedule,
        ]);
    }
}
