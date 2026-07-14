<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TeachersExport;
use App\Http\Controllers\Controller;
use App\Imports\TeachersImport;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $teachers = Teacher::withCount('courses')
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

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:200',
            'email'             => 'required|email|unique:teachers,email',
            'phone'             => 'nullable|string|max:20',
            'password'          => 'required|string|min:8|confirmed',
            'specialization_ar' => 'nullable|string|max:255',
            'specialization_en' => 'nullable|string|max:255',
            'bio_ar'            => 'nullable|string',
            'bio_en'            => 'nullable|string',
            'qualification_ar'  => 'nullable|string|max:200',
            'qualification_en'  => 'nullable|string|max:200',
            'years_of_experience' => 'nullable|integer|min:0',
            'gender'            => 'nullable|in:male,female',
            'nationality'       => 'nullable|string|max:100',
            'is_verified'       => 'boolean',
            'avatar'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        $data['is_verified']          = $request->boolean('is_verified');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = uploadImage('assets/uploads/teachers', $request->file('avatar'));
        }

        Teacher::create($data);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher created successfully.');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['courses' => fn ($q) => $q->withCount('enrollments')->latest()->limit(5)]);

        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:200',
            'email'             => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone'             => 'nullable|string|max:20',
            'password'          => 'nullable|string|min:8|confirmed',
            'specialization_ar' => 'nullable|string|max:255',
            'specialization_en' => 'nullable|string|max:255',
            'bio_ar'            => 'nullable|string',
            'bio_en'            => 'nullable|string',
            'qualification_ar'  => 'nullable|string|max:200',
            'qualification_en'  => 'nullable|string|max:200',
            'years_of_experience' => 'nullable|integer|min:0',
            'gender'            => 'nullable|in:male,female',
            'nationality'       => 'nullable|string|max:100',
            'is_active'         => 'boolean',
            'is_verified'       => 'boolean',
            
            'avatar'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        $data['is_active']            = $request->boolean('is_active');
        $data['is_verified']          = $request->boolean('is_verified');

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = uploadImage('assets/uploads/teachers', $request->file('avatar'));
        }

        $teacher->update($data);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }

    public function export(Request $request)
    {
        $filters  = $request->only(['search', 'is_active']);
        $filename = 'teachers_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new TeachersExport($filters), $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $importer = new TeachersImport();
        Excel::import($importer, $request->file('file'));

        $msg = "تم استيراد {$importer->imported} معلم بنجاح.";
        if ($importer->skipped) $msg .= " تم تخطي {$importer->skipped}.";
        if ($importer->errors)  $msg .= ' أخطاء: ' . implode(' | ', $importer->errors);

        return back()->with('success', $msg);
    }
}
