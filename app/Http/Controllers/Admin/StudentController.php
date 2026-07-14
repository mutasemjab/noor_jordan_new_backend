<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::withCount('enrollments')
            ->when($request->search, fn ($q, $s) => $q
                ->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
            )
            ->when($request->is_active !== null && $request->is_active !== '', fn ($q) =>
                $q->where('is_active', $request->boolean('is_active'))
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'national_id' => 'nullable|string|max:50|unique:students,national_id',
            'email'       => 'nullable|email|unique:students,email',
            'phone'       => 'nullable|string|max:20',
            'password'    => 'required|string|min:8|confirmed',
            'gender'      => 'nullable|in:male,female',
            'nationality' => 'nullable|string|max:100',
            'class_id'    => 'nullable|exists:classes,id',
            'avatar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = uploadImage('public/uploads/students', $request->file('avatar'));
        }

        Student::create($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['enrollments.course', 'examAttempts.exam']);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'national_id' => 'nullable|string|max:50|unique:students,national_id,' . $student->id,
            'email'       => 'nullable|email|unique:students,email,' . $student->id,
            'phone'       => 'nullable|string|max:20',
            'password'    => 'nullable|string|min:8|confirmed',
            'gender'      => 'nullable|in:male,female',
            'nationality' => 'nullable|string|max:100',
            'class_id'    => 'nullable|exists:classes,id',
            'is_active'   => 'boolean',
            'avatar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = uploadImage('public/uploads/students', $request->file('avatar'));
        }

        $student->update($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function resetDevice(Student $student)
    {
        $student->update(['deviceId' => null]);

        return back()->with('success', 'تم إعادة تعيين الجهاز. يمكن للطالب الآن تسجيل الدخول من جهاز جديد.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function export(Request $request)
    {
        $filters = $request->only(['search', 'is_active']);
        $filename = 'students_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new StudentsExport($filters), $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $importer = new StudentsImport();
        Excel::import($importer, $request->file('file'));

        $msg = "تم استيراد {$importer->imported} طالب بنجاح.";
        if ($importer->skipped)  $msg .= " تم تخطي {$importer->skipped}.";
        if ($importer->errors)   $msg .= ' أخطاء: ' . implode(' | ', $importer->errors);

        return back()->with('success', $msg);
    }
}
