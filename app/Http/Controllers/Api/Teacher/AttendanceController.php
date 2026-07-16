<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Attendance;
use App\Models\ClassSubject;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'date'     => 'required|date',
        ]);

        $teacher = $request->user();
        $this->authorizeClass($teacher->id, $request->class_id);

        $students = Student::where('class_id', $request->class_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $existing = Attendance::where('class_id', $request->class_id)
            ->whereDate('date', $request->date)
            ->when($request->period, fn ($q) => $q->where('period', $request->period))
            ->get()
            ->keyBy('student_id');

        return $this->success([
            'students' => $students->map(fn ($s) => [
                'id'     => $s->id,
                'name'   => $s->name,
                'status' => $existing[$s->id]?->status ?? 'present',
                'notes'  => $existing[$s->id]?->notes,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'class_id'              => 'required|exists:classes,id',
            'date'                  => 'required|date',
            'period'                => 'nullable|integer|min:1',
            'attendance'            => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status'   => 'required|in:present,absent,late,excused',
            'attendance.*.notes'    => 'nullable|string|max:300',
        ]);

        $teacher = $request->user();
        $this->authorizeClass($teacher->id, $data['class_id']);

        foreach ($data['attendance'] as $item) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $item['student_id'],
                    'class_id'   => $data['class_id'],
                    'date'       => $data['date'],
                    'period'     => $data['period'] ?? null,
                ],
                [
                    'status' => $item['status'],
                    'notes'  => $item['notes'] ?? null,
                ]
            );
        }

        return $this->success(null, 'تم حفظ الغياب بنجاح.');
    }

    private function authorizeClass(int $teacherId, int $classId): void
    {
        $teaches = ClassSubject::where('class_id', $classId)
            ->where('teacher_id', $teacherId)
            ->exists();

        if (! $teaches) {
            abort(response()->json(['status' => false, 'message' => 'غير مصرح.'], 403));
        }
    }
}
