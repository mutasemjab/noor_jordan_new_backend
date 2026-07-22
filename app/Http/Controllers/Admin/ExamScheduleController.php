<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ExamScheduleController extends Controller
{
    public function index()
    {
        $examSchedules = ExamSchedule::with('schoolClass')
            ->orderByDesc('created_at')
            ->get();

        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();

        return view('admin.exam_schedules.index', compact('examSchedules', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:200',
            'class_id' => 'nullable|exists:classes,id',
            'image'    => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:8192',
        ], [
            'name.required'  => 'يرجى إدخال اسم جدول الامتحانات.',
            'image.required' => 'يرجى رفع صورة الجدول.',
            'image.image'    => 'الملف يجب أن يكون صورة.',
            'image.max'      => 'حجم الصورة يجب ألا يتجاوز 8 ميغابايت.',
        ]);

        $file     = $request->file('image');
        $filename = 'exam_schedule_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets/uploads/exam-schedules'), $filename);

        ExamSchedule::create([
            'name'     => $request->name,
            'class_id' => $request->class_id ?: null,
            'image'    => $filename,
        ]);

        return redirect()->route('admin.exam-schedules.index')
            ->with('success', 'تم إضافة جدول الامتحانات بنجاح.');
    }

    public function edit(ExamSchedule $examSchedule)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        return view('admin.exam_schedules.edit', compact('examSchedule', 'classes'));
    }

    public function update(Request $request, ExamSchedule $examSchedule)
    {
        $request->validate([
            'name'     => 'required|string|max:200',
            'class_id' => 'nullable|exists:classes,id',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:8192',
        ]);

        $data = [
            'name'     => $request->name,
            'class_id' => $request->class_id ?: null,
        ];

        if ($request->hasFile('image')) {
            // Remove old image
            if ($examSchedule->image) {
                $oldPath = public_path('assets/uploads/exam-schedules/' . $examSchedule->image);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $file     = $request->file('image');
            $filename = 'exam_schedule_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/uploads/exam-schedules'), $filename);
            $data['image'] = $filename;
        }

        $examSchedule->update($data);

        return redirect()->route('admin.exam-schedules.index')
            ->with('success', 'تم تحديث جدول الامتحانات بنجاح.');
    }

    public function destroy(ExamSchedule $examSchedule)
    {
        if ($examSchedule->image) {
            $path = public_path('assets/uploads/exam-schedules/' . $examSchedule->image);
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $examSchedule->delete();

        return redirect()->route('admin.exam-schedules.index')
            ->with('success', 'تم حذف جدول الامتحانات.');
    }
}
