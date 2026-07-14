<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\{Course, Lesson, Material, Unit};
use Illuminate\Http\Request;

class UnitController extends Controller
{
    private function teacher()
    {
        return auth()->guard('teacher')->user();
    }

    public function store(Request $request, int $courseId)
    {
        $course = Course::findOrFail($courseId);

        abort_if($course->teacher_id !== $this->teacher()->id, 403);

        $data = $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'order_index'    => 'nullable|integer|min:0',
        ]);

        $data['course_id'] = $courseId;
        $data['order_index'] ??= $course->units()->max('order_index') + 1;

        $course->units()->create($data);

        return back()->with('success', 'Unit added.');
    }

    public function update(Request $request, int $courseId, int $unitId)
    {
        $unit = Unit::where('course_id', $courseId)->findOrFail($unitId);

        abort_if($unit->course->teacher_id !== $this->teacher()->id, 403);

        $data = $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'order_index'    => 'nullable|integer|min:0',
            'is_published'   => 'boolean',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $unit->update($data);

        return back()->with('success', 'Unit updated.');
    }

    public function destroy(int $courseId, int $unitId)
    {
        $unit = Unit::where('course_id', $courseId)->findOrFail($unitId);

        abort_if($unit->course->teacher_id !== $this->teacher()->id, 403);

        $unit->delete();

        return back()->with('success', 'Unit deleted.');
    }

    // ── Lesson management ─────────────────────────────────────────────

    private function deleteFile(?string $path): void
    {
        if ($path) {
            $full = public_path($path);
            if (file_exists($full)) {
                unlink($full);
            }
        }
    }

    public function storeLesson(Request $request, int $courseId, int $unitId)
    {
        $unit = Unit::where('course_id', $courseId)->findOrFail($unitId);

        abort_if($unit->course->teacher_id !== $this->teacher()->id, 403);

        $data = $request->validate([
            'title_ar'         => 'required|string|max:255',
            'title_en'         => 'nullable|string|max:255',
            'lesson_type'      => 'required|in:video,pdf',
            'video_url'        => 'required_if:lesson_type,video|nullable|url|max:500',
            'lesson_file'      => 'required_if:lesson_type,pdf|nullable|file|mimes:pdf|max:51200',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_free'          => 'boolean',
            'order_index'      => 'nullable|integer|min:0',
        ]);

        $data['unit_id']     = $unitId;
        $data['is_free']     = $request->boolean('is_free');
        $data['order_index'] ??= $unit->lessons()->max('order_index') + 1;

        if ($data['lesson_type'] === 'pdf') {
            $data['file_path'] = uploadImage('assets/uploads/lessons', $request->file('lesson_file'));
            $data['video_url'] = null;
        } else {
            $data['file_path'] = null;
        }

        unset($data['lesson_file']);
        $unit->lessons()->create($data);

        return back()->with('success', __('messages.lesson_added'));
    }

    public function updateLesson(Request $request, int $courseId, int $unitId, int $lessonId)
    {
        $lesson = Lesson::where('unit_id', $unitId)->findOrFail($lessonId);

        abort_if($lesson->unit->course->teacher_id !== $this->teacher()->id, 403);

        $data = $request->validate([
            'title_ar'         => 'required|string|max:255',
            'title_en'         => 'nullable|string|max:255',
            'lesson_type'      => 'required|in:video,pdf',
            'video_url'        => 'required_if:lesson_type,video|nullable|url|max:500',
            'lesson_file'      => 'nullable|file|mimes:pdf|max:51200',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_free'          => 'boolean',
        ]);

        $data['is_free'] = $request->boolean('is_free');

        if ($data['lesson_type'] === 'pdf') {
            if ($request->hasFile('lesson_file')) {
                $this->deleteFile('assets/uploads/lessons/' . $lesson->file_path);
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

    public function destroyLesson(int $courseId, int $unitId, int $lessonId)
    {
        $lesson = Lesson::where('unit_id', $unitId)->findOrFail($lessonId);

        abort_if($lesson->unit->course->teacher_id !== $this->teacher()->id, 403);

        $lesson->delete();
        $lesson->unit->decrement('total_videos');

        return back()->with('success', 'Lesson deleted.');
    }

    // ── Material management ───────────────────────────────────────────

    public function storeMaterial(Request $request, int $courseId, int $unitId)
    {
        $unit = Unit::where('course_id', $courseId)->findOrFail($unitId);

        abort_if($unit->course->teacher_id !== $this->teacher()->id, 403);

        $data = $request->validate([
            'title_ar'        => 'required|string|max:255',
            'title_en'        => 'required|string|max:255',
            'description_ar'  => 'nullable|string',
            'description_en'  => 'nullable|string',
            'file_type'       => 'required|in:pdf,worksheet,summary,presentation,other',
            'pages_count'     => 'nullable|integer|min:0',
            'is_downloadable' => 'boolean',
            'is_free'         => 'boolean',
            'order_index'     => 'nullable|integer|min:0',
            'file'            => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $filename = uniqid() . '_' . time() . '.' . strtolower($file->getClientOriginalExtension());
            $file->move(base_path('public/uploads/materials'), $filename);
            $data['file_path']    = $filename;
            $data['file_size_mb'] = round($file->getSize() / 1048576, 2);
        }

        $data['unit_id']          = $unitId;
        $data['is_downloadable']  = $request->boolean('is_downloadable');
        $data['is_free']          = $request->boolean('is_free');

        $unit->materials()->create($data);
        $unit->increment('total_pdfs');

        return back()->with('success', 'Material uploaded.');
    }

    public function destroyMaterial(int $courseId, int $unitId, int $materialId)
    {
        $material = Material::where('unit_id', $unitId)->findOrFail($materialId);

        abort_if($material->unit->course->teacher_id !== $this->teacher()->id, 403);

        $material->delete();
        $material->unit->decrement('total_pdfs');

        return back()->with('success', 'Material deleted.');
    }
}
