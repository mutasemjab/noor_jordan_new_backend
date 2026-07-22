<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubject;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $classIds = ClassSubject::where('teacher_id', $teacher->id)
            ->pluck('class_id')
            ->unique()
            ->values();

        $classes = SchoolClass::whereIn('id', $classIds)
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'id'             => $c->id,
                'name'           => $c->name,
                'schedule_image' => $c->schedule_image
                    ? asset('assets/uploads/schedules/' . $c->schedule_image)
                    : null,
            ]);

        return $this->success($classes);
    }

    public function examSchedules(Request $request): JsonResponse
    {
        $schedules = ExamSchedule::with('schoolClass:id,name')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($es) => [
                'id'         => $es->id,
                'name'       => $es->name,
                'class_name' => $es->schoolClass?->name,
                'image'      => $es->image
                    ? asset('assets/uploads/exam-schedules/' . $es->image)
                    : null,
                'created_at' => $es->created_at->toDateString(),
            ]);

        return $this->success($schedules);
    }
}
