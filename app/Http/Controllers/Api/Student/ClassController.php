<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ClassSubjectVideo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    use ApiResponse;

    public function mySubjects(Request $request): JsonResponse
    {
        $student = $request->user();
        $class   = $student->schoolClass;

        if (! $class) {
            return $this->error('الطالب غير مسجل في أي صف.', 404);
        }

        $class->load(['homeroomTeacher', 'classSubjects.subject', 'classSubjects.teacher']);

        return $this->success([
            'class' => [
                'id'               => $class->id,
                'name'             => $class->name,
                'homeroom_teacher' => $class->homeroomTeacher ? [
                    'id'     => $class->homeroomTeacher->id,
                    'name'   => $class->homeroomTeacher->name,
                    'avatar' => $class->homeroomTeacher->avatar
                        ? asset('assets/uploads/teachers/' . $class->homeroomTeacher->avatar)
                        : null,
                ] : null,
            ],
            'subjects' => $class->classSubjects->map(fn ($cs) => [
                'id'          => $cs->subject->id,
                'name_ar'     => $cs->subject->name_ar,
                'name_en'     => $cs->subject->name_en,
                'icon'        => $cs->subject->icon,
                'color_class' => $cs->subject->color_class,
                'teacher'     => $cs->teacher ? [
                    'id'     => $cs->teacher->id,
                    'name'   => $cs->teacher->name,
                    'avatar' => $cs->teacher->avatar
                        ? asset('assets/uploads/teachers/' . $cs->teacher->avatar)
                        : null,
                    'phone'  => $cs->teacher->phone,
                ] : null,
            ]),
        ], 'تم جلب مواد الصف بنجاح.');
    }

    public function subjectVideos(Request $request, int $subjectId): JsonResponse
    {
        $student = $request->user();

        if (! $student->class_id) {
            return $this->error('الطالب غير مسجل في أي صف.', 404);
        }

        $videos = ClassSubjectVideo::where('class_id', $student->class_id)
            ->where('subject_id', $subjectId)
            ->orderBy('order_index')
            ->get()
            ->map(fn ($v) => [
                'id'           => $v->id,
                'title'        => $v->title,
                'youtube_url'  => $v->youtube_url,
                'youtube_id'   => $v->youtube_id,
                'thumbnail'    => $v->thumbnail,
                'order_index'  => $v->order_index,
            ]);

        return $this->success($videos);
    }
}
