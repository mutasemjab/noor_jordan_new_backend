<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PeriodSetting;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $periods = PeriodSetting::orderBy('period_number')->get();

        $students    = collect();
        $attendance  = collect();
        $selectedClass = null;

        if ($request->filled('class_id') && $request->filled('date')) {
            $selectedClass = SchoolClass::findOrFail($request->class_id);

            $students = Student::where('class_id', $request->class_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            $attendance = Attendance::where('class_id', $request->class_id)
                ->whereDate('date', $request->date)
                ->when($request->period, fn ($q) => $q->where('period', $request->period))
                ->get()
                ->keyBy('student_id');
        }

        return view('admin.attendance.index', compact(
            'classes', 'periods', 'students', 'attendance', 'selectedClass'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'date'       => 'required|date',
            'period'     => 'nullable|integer|min:1',
            'attendance' => 'required|array',
        ]);

        foreach ($request->attendance as $studentId => $status) {
            if (! in_array($status, ['present', 'absent', 'late', 'excused'])) continue;

            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_id'   => $request->class_id,
                    'date'       => $request->date,
                    'period'     => $request->period ?: null,
                ],
                [
                    'status' => $status,
                    'notes'  => $request->input("notes.{$studentId}"),
                ]
            );
        }

        return back()->with('success', 'تم حفظ الغياب بنجاح.');
    }
}
