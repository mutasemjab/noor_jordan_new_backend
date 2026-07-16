<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $classes  = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $subjects = collect();
        $students = collect();
        $grades   = collect();
        $selectedClass   = null;
        $selectedSubject = null;

        if ($request->filled('class_id')) {
            $selectedClass = SchoolClass::findOrFail($request->class_id);
            $subjects = ClassSubject::where('class_id', $request->class_id)
                ->with('subject')
                ->get()
                ->map(fn ($cs) => $cs->subject);
        }

        if ($request->filled('class_id') && $request->filled('subject_id')) {
            $selectedSubject = Subject::findOrFail($request->subject_id);

            $students = Student::where('class_id', $request->class_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            $grades = StudentGrade::where('class_id', $request->class_id)
                ->where('subject_id', $request->subject_id)
                ->when($request->title, fn ($q) => $q->where('title', $request->title))
                ->get()
                ->groupBy('student_id');
        }

        return view('admin.grades.index', compact(
            'classes', 'subjects', 'students', 'grades', 'selectedClass', 'selectedSubject'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'title'      => 'required|string|max:200',
            'max_score'  => 'required|numeric|min:1',
            'graded_at'  => 'required|date',
            'grades'     => 'required|array',
        ]);

        $classSubject = ClassSubject::where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->first();

        foreach ($request->grades as $studentId => $score) {
            if ($score === '' || $score === null) continue;

            StudentGrade::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_id'   => $request->class_id,
                    'subject_id' => $request->subject_id,
                    'title'      => $request->title,
                ],
                [
                    'teacher_id' => $classSubject?->teacher_id,
                    'score'      => $score,
                    'max_score'  => $request->max_score,
                    'graded_at'  => $request->graded_at,
                ]
            );
        }

        return back()->with('success', 'تم حفظ العلامات بنجاح.');
    }
}
