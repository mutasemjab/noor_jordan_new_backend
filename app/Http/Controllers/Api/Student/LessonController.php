<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    use ApiResponse;

    // GET /courses/{id}/units  — list units with lessons (respects free/locked)
    public function courseUnits(Request $request, int $courseId): JsonResponse
    {
        $course = Course::where('is_published', true)->findOrFail($courseId);

        $isEnrolled = false;
        if ($request->user()) {
            $isEnrolled = Enrollment::where('student_id', $request->user()->id)
                ->where('course_id', $courseId)
                ->where('is_active', true)
                ->exists();
        }

        $units = $course->units()
            ->where('is_published', true)
            ->orderBy('order_index')
            ->with(['lessons' => fn ($q) => $q->where('is_published', true)->orderBy('order_index'),
                    'exams'   => fn ($q) => $q->where('is_published', true)])
            ->get();

        $data = $units->map(fn ($unit) => [
            'id'          => $unit->id,
            'title'       => $unit->title,
            'title_ar'    => $unit->title_ar,
            'title_en'    => $unit->title_en,
            'description' => $unit->description,
            'order_index' => $unit->order_index,
            'lessons'     => $unit->lessons->map(fn ($lesson) => $this->lessonCard($lesson, $isEnrolled)),
            'exams'       => $unit->exams->map(fn ($exam) => [
                'id'               => $exam->id,
                'title'            => $exam->title,
                'duration_minutes' => $exam->duration_minutes,
                'total_questions'  => $exam->total_questions,
                'total_marks'      => $exam->total_marks,
            ]),
        ]);

        return $this->success([
            'course_id'   => $course->id,
            'course_name' => $course->title,
            'is_enrolled' => $isEnrolled,
            'units'       => $data,
        ]);
    }

    // GET /lessons/{id}  — lesson detail (video + PDF); requires enrollment for paid
    public function show(Request $request, int $lessonId): JsonResponse
    {
        $lesson = Lesson::where('is_published', true)
            ->with('unit.course')
            ->findOrFail($lessonId);

        $course = $lesson->unit?->course;

        $isEnrolled = false;
        if ($request->user() && $course) {
            $isEnrolled = Enrollment::where('student_id', $request->user()->id)
                ->where('course_id', $course->id)
                ->where('is_active', true)
                ->exists();
        }

        // Block paid content if not enrolled
        if (! $lesson->is_free && ! $isEnrolled) {
            return $this->error('يجب تفعيل الدورة للوصول إلى هذا الدرس', 403);
        }

        return $this->success([
            'id'               => $lesson->id,
            'title'            => $lesson->title,
            'title_ar'         => $lesson->title_ar,
            'title_en'         => $lesson->title_en,
            'lesson_type'      => $lesson->lesson_type,
            'video_url'        => $lesson->video_url,    // YouTube URL — use WebView
            'file_url'         => $lesson->file_path ? asset('assets/uploads/lessons/' . $lesson->file_path) : null,
            'duration_minutes' => $lesson->duration_minutes,
            'is_free'          => $lesson->is_free,
            'is_enrolled'      => $isEnrolled,
            'unit_id'          => $lesson->unit_id,
            'course_id'        => $course?->id,
        ]);
    }

    // GET /units/{id}/exams  — exams attached to a unit
    public function unitExams(Request $request, int $unitId): JsonResponse
    {
        $unit = Unit::where('is_published', true)->findOrFail($unitId);

        $isEnrolled = false;
        if ($request->user()) {
            $isEnrolled = Enrollment::where('student_id', $request->user()->id)
                ->where('course_id', $unit->course_id)
                ->where('is_active', true)
                ->exists();
        }

        $exams = $unit->exams()->where('is_published', true)->get();

        return $this->success([
            'unit_id'     => $unit->id,
            'unit_title'  => $unit->title,
            'is_enrolled' => $isEnrolled,
            'exams'       => $exams->map(fn ($e) => [
                'id'               => $e->id,
                'title'            => $e->title,
                'title_ar'         => $e->title_ar,
                'title_en'         => $e->title_en,
                'duration_minutes' => $e->duration_minutes,
                'total_questions'  => $e->total_questions,
                'total_marks'      => $e->total_marks,
                'pass_marks'       => $e->pass_marks,
                'difficulty_level' => $e->difficulty_level,
            ]),
        ]);
    }

    private function lessonCard(Lesson $lesson, bool $isEnrolled): array
    {
        $locked = ! $lesson->is_free && ! $isEnrolled;

        return [
            'id'               => $lesson->id,
            'title'            => $lesson->title,
            'title_ar'         => $lesson->title_ar,
            'title_en'         => $lesson->title_en,
            'lesson_type'      => $lesson->lesson_type,
            'duration_minutes' => $lesson->duration_minutes,
            'order_index'      => $lesson->order_index,
            'is_free'          => $lesson->is_free,
            'is_locked'        => $locked,
            // Only expose URLs for accessible lessons
            'video_url'        => $locked ? null : $lesson->video_url,
            'file_url'         => ($locked || ! $lesson->file_path) ? null : asset('assets/uploads/lessons/' . $lesson->file_path),
        ];
    }
}
