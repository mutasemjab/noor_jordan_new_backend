<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $student = $request->user();

        $records = Attendance::where('student_id', $student->id)
            ->when($request->month, fn ($q) => $q->whereMonth('date', $request->month))
            ->when($request->year,  fn ($q) => $q->whereYear('date', $request->year ?? now()->year))
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn ($a) => [
                'id'     => $a->id,
                'date'   => $a->date->format('Y-m-d'),
                'period' => $a->period,
                'status' => $a->status,
                'status_label' => Attendance::$statuses[$a->status] ?? $a->status,
                'notes'  => $a->notes,
            ]);

        $summary = [
            'present' => $records->where('status', 'present')->count(),
            'absent'  => $records->where('status', 'absent')->count(),
            'late'    => $records->where('status', 'late')->count(),
            'excused' => $records->where('status', 'excused')->count(),
        ];

        return $this->success(['summary' => $summary, 'records' => $records]);
    }
}
