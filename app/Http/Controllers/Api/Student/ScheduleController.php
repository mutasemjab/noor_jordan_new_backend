<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ExamSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $student = $request->user();

        if (! $student->class_id) {
            return $this->error('الطالب غير مسجل في أي صف.', 404);
        }

        $class = $student->schoolClass;

        return $this->success([
            'class_id'       => $class->id,
            'class_name'     => $class->name,
            'schedule_image' => $class->schedule_image
                ? asset('assets/uploads/schedules/' . $class->schedule_image)
                : null,
        ]);
    }

    public function examSchedules(Request $request): JsonResponse
    {
        $student = $request->user();

        $schedules = ExamSchedule::where(function ($q) use ($student) {
            $q->whereNull('class_id')
              ->orWhere('class_id', $student->class_id);
        })
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
