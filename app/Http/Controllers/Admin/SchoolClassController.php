<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::withCount('students')
            ->with('homeroomTeacher')
            ->orderBy('name')
            ->get();

        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers = Teacher::where('is_active', true)->orderBy('name')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:100|unique:classes,name',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
            'is_active'           => 'boolean',
        ]);

        SchoolClass::create($data);

        return redirect()->route('admin.classes.index')
            ->with('success', 'تم إنشاء الصف بنجاح.');
    }

    public function show(SchoolClass $class)
    {
        $class->load(['homeroomTeacher', 'classSubjects.subject', 'classSubjects.teacher']);

        $teachers       = Teacher::where('is_active', true)->orderBy('name')->get();
        $assignedIds    = $class->classSubjects->pluck('subject_id')->toArray();
        $availableSubjects = Subject::where('is_active', true)
            ->whereNotIn('id', $assignedIds)
            ->orderBy('name_ar')
            ->get();

        return view('admin.classes.show', compact('class', 'teachers', 'availableSubjects'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:100|unique:classes,name,' . $class->id,
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
            'is_active'           => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $class->update($data);

        return redirect()->route('admin.classes.show', $class->id)
            ->with('success', 'تم تحديث بيانات الصف.');
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();
        return redirect()->route('admin.classes.index')
            ->with('success', 'تم حذف الصف.');
    }

    public function addSubject(Request $request, SchoolClass $class)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        ClassSubject::firstOrCreate(
            ['class_id' => $class->id, 'subject_id' => $data['subject_id']],
            ['teacher_id' => $data['teacher_id'] ?? null]
        );

        return redirect()->route('admin.classes.show', $class->id)
            ->with('success', 'تم إضافة المادة للصف.');
    }

    public function updateSubject(Request $request, SchoolClass $class, Subject $subject)
    {
        $data = $request->validate([
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        ClassSubject::where('class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->update(['teacher_id' => $data['teacher_id'] ?? null]);

        return redirect()->route('admin.classes.show', $class->id)
            ->with('success', 'تم تحديث المعلم.');
    }

    public function removeSubject(SchoolClass $class, Subject $subject)
    {
        ClassSubject::where('class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->delete();

        return redirect()->route('admin.classes.show', $class->id)
            ->with('success', 'تم إزالة المادة من الصف.');
    }

    public function schedule(SchoolClass $class)
    {
        return view('admin.classes.schedule', compact('class'));
    }

    public function updateSchedule(Request $request, SchoolClass $class)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:8192',
        ], [
            'image.required' => 'يرجى اختيار صورة الجدول.',
            'image.image'    => 'الملف المرفق يجب أن يكون صورة.',
            'image.max'      => 'حجم الصورة يجب ألا يتجاوز 8 ميغابايت.',
        ]);

        // Remove old image
        if ($class->schedule_image) {
            $oldPath = base_path('assets/uploads/schedules/' . $class->schedule_image);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $file     = $request->file('image');
        $filename = 'schedule_' . $class->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(base_path('assets/uploads/schedules'), $filename);

        $class->update(['schedule_image' => $filename]);

        return back()->with('success', 'تم رفع صورة الجدول بنجاح.');
    }
}
