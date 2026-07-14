<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Material;
use App\Models\Unit;
use Illuminate\Http\Request;

class CourseContentController extends Controller
{
    // ── Units ────────────────────────────────────────────────────────────

    public function storeUnit(Request $request, int $courseId)
    {
        $course = Course::findOrFail($courseId);

        $data = $request->validate([
            'title_ar'    => 'required|string|max:255',
            'title_en'    => 'nullable|string|max:255',
            'order_index' => 'nullable|integer|min:0',
        ]);

        $data['order_index'] = $data['order_index'] ?? ($course->units()->max('order_index') + 1);

        $course->units()->create($data);

        return back()->with('success', __('messages.unit_added'));
    }

    public function destroyUnit(int $id)
    {
        $unit = Unit::findOrFail($id);

        foreach ($unit->materials as $mat) {
            $this->deleteFile($mat->file_path);
        }

        $unit->delete();

        return back()->with('success', __('messages.unit_deleted'));
    }

    // ── Lessons (video or PDF) ────────────────────────────────────────────

    public function storeLesson(Request $request, int $unitId)
    {
        $unit = Unit::findOrFail($unitId);

        $lessonType = $request->input('lesson_type', 'video');

        $data = $request->validate([
            'title_ar'         => 'required|string|max:255',
            'title_en'         => 'nullable|string|max:255',
            'lesson_type'      => 'required|in:video,pdf',
            'video_url'        => 'required_if:lesson_type,video|nullable|url|max:500',
            'lesson_file'      => 'required_if:lesson_type,pdf|nullable|file|mimes:pdf|max:51200',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_free'          => 'boolean',
            'order_index'      => 'nullable|integer|min:0',
        ]);

        $data['is_free']      = $request->boolean('is_free');
        $data['order_index']  = $data['order_index'] ?? ($unit->lessons()->max('order_index') + 1);
        $data['is_published'] = true;

        if ($lessonType === 'pdf' && $request->hasFile('lesson_file')) {
            $data['file_path'] = uploadImage('assets/uploads/lessons', $request->file('lesson_file'));
            $data['video_url'] = null;
        } elseif ($lessonType === 'video') {
            $data['file_path'] = null;
        }

        unset($data['lesson_file']);

        $unit->lessons()->create($data);
        $unit->increment('total_videos');

        return back()->with('success', __('messages.lesson_added'));
    }

    public function updateLesson(Request $request, int $id)
    {
        $lesson = Lesson::findOrFail($id);

        $data = $request->validate([
            'title_ar'         => 'required|string|max:255',
            'title_en'         => 'nullable|string|max:255',
            'lesson_type'      => 'required|in:video,pdf',
            'video_url'        => 'required_if:lesson_type,video|nullable|url|max:500',
            'lesson_file'      => 'nullable|file|mimes:pdf|max:51200',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_free'          => 'boolean',
        ]);

        $data['is_free'] = $request->boolean('is_free');

        if ($data['lesson_type'] === 'pdf') {
            if ($request->hasFile('lesson_file')) {
                if ($lesson->file_path) {
                    $this->deleteFile('assets/uploads/lessons/' . $lesson->file_path);
                }
                $data['file_path'] = uploadImage('assets/uploads/lessons', $request->file('lesson_file'));
            }
            $data['video_url'] = null;
        } else {
            $data['file_path'] = null;
        }

        unset($data['lesson_file']);

        $lesson->update($data);

        return back()->with('success', __('messages.lesson_updated'));
    }

    public function destroyLesson(int $id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($lesson->lesson_type === 'pdf' && $lesson->file_path) {
            $this->deleteFile('assets/uploads/lessons/' . $lesson->file_path);
        }

        $lesson->unit->decrement('total_videos');
        $lesson->delete();

        return back()->with('success', __('messages.lesson_deleted'));
    }

    // ── Materials (PDFs) ─────────────────────────────────────────────────

    public function storeMaterial(Request $request, int $unitId)
    {
        $unit = Unit::findOrFail($unitId);

        $data = $request->validate([
            'title_ar'        => 'required|string|max:255',
            'title_en'        => 'nullable|string|max:255',
            'is_free'         => 'boolean',
            'is_downloadable' => 'boolean',
            'order_index'     => 'nullable|integer|min:0',
            'pdf_file'        => 'required|mimes:pdf|max:20480',
        ]);

        $data['file_type']      = 'pdf';
        $data['is_free']        = $request->boolean('is_free');
        $data['is_downloadable']= $request->boolean('is_downloadable');
        $data['order_index']    = $data['order_index'] ?? ($unit->materials()->max('order_index') + 1);

        if ($request->hasFile('pdf_file')) {
            $data['file_path'] = uploadImage('assets/uploads/materials', $request->file('pdf_file'));
            $size = $request->file('pdf_file')->getSize();
            $data['file_size_mb'] = round($size / 1048576, 2);
        }

        unset($data['pdf_file']);

        $unit->materials()->create($data);
        $unit->increment('total_pdfs');

        return back()->with('success', __('messages.material_added'));
    }

    public function destroyMaterial(int $id)
    {
        $mat = Material::findOrFail($id);
        $this->deleteFile($mat->file_path);
        $mat->unit->decrement('total_pdfs');
        $mat->delete();

        return back()->with('success', __('messages.material_deleted'));
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            $full = public_path($path);
            if (file_exists($full)) {
                unlink($full);
            }
        }
    }
}
