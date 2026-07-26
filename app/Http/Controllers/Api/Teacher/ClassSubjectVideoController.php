<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubject;
use App\Models\ClassSubjectVideo;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassSubjectVideoController extends Controller
{
    use ApiResponse;

    // GET /classes/{class}/videos
    public function index(Request $request, SchoolClass $class): JsonResponse
    {
        $teacher = $request->user();

        $subjectIds = $this->subjectIdsInClass($teacher, $class);

        if ($subjectIds->isEmpty()) {
            return $this->error('غير مصرح بالوصول لهذا الصف.', 403);
        }

        $videos = ClassSubjectVideo::where('class_id', $class->id)
            ->whereIn('subject_id', $subjectIds)
            ->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->subject_id))
            ->with('subject')
            ->orderBy('subject_id')
            ->orderBy('order_index')
            ->get();

        return $this->success($videos->map(fn ($v) => $this->videoCard($v))->values());
    }

    // POST /classes/{class}/videos
    public function store(Request $request, SchoolClass $class): JsonResponse
    {
        $teacher = $request->user();

        $data = $request->validate([
            'subject_id'  => ['required', 'exists:subjects,id'],
            'title'       => ['required', 'string', 'max:255'],
            'youtube_url' => ['required', 'url'],
        ]);

        if (! $this->teachesSubjectInClass($teacher, $class->id, $data['subject_id'])) {
            return $this->error('أنت لا تُدرّس هذه المادة لهذا الصف.', 403);
        }

        $maxOrder = ClassSubjectVideo::where('class_id', $class->id)
            ->where('subject_id', $data['subject_id'])
            ->max('order_index') ?? 0;

        $video = ClassSubjectVideo::create([
            'class_id'    => $class->id,
            'subject_id'  => $data['subject_id'],
            'title'       => $data['title'],
            'youtube_url' => $data['youtube_url'],
            'order_index' => $maxOrder + 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'OK',
            'data'    => $this->videoCard($video->load('subject')),
        ], 201);
    }

    // DELETE /classes/{class}/videos/{video}
    public function destroy(Request $request, SchoolClass $class, ClassSubjectVideo $video): JsonResponse
    {
        $teacher = $request->user();

        if ((int) $video->class_id !== (int) $class->id) {
            return $this->error('الفيديو لا ينتمي لهذا الصف.', 404);
        }

        if (! $this->teachesSubjectInClass($teacher, $class->id, $video->subject_id)) {
            return $this->error('غير مصرح بحذف هذا الفيديو.', 403);
        }

        $video->delete();

        return $this->success(null, 'OK');
    }

    private function subjectIdsInClass(Teacher $teacher, SchoolClass $class)
    {
        return ClassSubject::where('teacher_id', $teacher->id)
            ->where('class_id', $class->id)
            ->pluck('subject_id');
    }

    private function teachesSubjectInClass(Teacher $teacher, int $classId, int $subjectId): bool
    {
        return ClassSubject::where('teacher_id', $teacher->id)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->exists();
    }

    private function videoCard(ClassSubjectVideo $video): array
    {
        return [
            'id'          => $video->id,
            'title'       => $video->title,
            'youtube_url' => $video->youtube_url,
            'thumbnail'   => $video->thumbnail,
            'subject'     => ['id' => $video->subject?->id, 'name' => $video->subject?->name],
        ];
    }
}
