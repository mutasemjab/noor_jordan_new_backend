<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\ProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    use ApiResponse;

    public function __construct(private ProgressService $progress) {}

    /**
     * POST /lessons/{id}/progress
     *
     * Called by the mobile app:
     *   - Periodically every ~30s while watching (watch_seconds only)
     *   - Once when the video ends (is_completed = true)
     */
    public function update(Request $request, int $lessonId): JsonResponse
    {
        $request->validate([
            'watch_seconds' => ['required', 'integer', 'min:0'],
            'is_completed'  => ['sometimes', 'boolean'],
        ]);

        $lesson  = Lesson::where('is_published', true)->with('unit')->findOrFail($lessonId);
        $student = $request->user();
        $courseId = $lesson->unit?->course_id;

        // Verify enrollment for paid lessons
        if (! $lesson->is_free && $courseId) {
            $enrolled = Enrollment::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->where('is_active', true)
                ->exists();

            if (! $enrolled) {
                return $this->error('يجب تفعيل الدورة أولاً', 403);
            }
        }

        $isCompleted = $request->boolean('is_completed', false);

        // Upsert progress record
        $record = LessonProgress::updateOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lessonId],
            [
                'watch_seconds' => $request->watch_seconds,
                'is_completed'  => $isCompleted,
                'completed_at'  => $isCompleted ? now() : null,
            ]
        );

        // Recalculate enrollment progress only when lesson is marked complete
        $courseProgress = null;
        if ($isCompleted && $courseId) {
            $percentage = $this->progress->recalculate($student->id, $courseId);
            $courseProgress = [
                'percentage'  => $percentage,
                'course_id'   => $courseId,
            ];
        }

        return $this->success([
            'lesson_id'      => $lessonId,
            'watch_seconds'  => $record->watch_seconds,
            'is_completed'   => $record->is_completed,
            'course_progress'=> $courseProgress,
        ]);
    }

    /**
     * GET /courses/{id}/my-progress
     *
     * Returns full progress snapshot for a course.
     */
    public function courseProgress(Request $request, int $courseId): JsonResponse
    {
        $student  = $request->user();
        $snapshot = $this->progress->snapshot($student->id, $courseId);

        // Lesson-level completed IDs for the app to mark checkmarks
        $completedLessonIds = LessonProgress::where('student_id', $student->id)
            ->where('is_completed', true)
            ->pluck('lesson_id');

        // Last watched positions
        $watchPositions = LessonProgress::where('student_id', $student->id)
            ->get(['lesson_id', 'watch_seconds', 'is_completed'])
            ->keyBy('lesson_id')
            ->map(fn ($r) => [
                'watch_seconds' => $r->watch_seconds,
                'is_completed'  => $r->is_completed,
            ]);

        return $this->success([
            'course_id'            => $courseId,
            'percentage'           => $snapshot['percentage'],
            'completed_lessons'    => $snapshot['completed_lessons'],
            'total_lessons'        => $snapshot['total_lessons'],
            'completed_exams'      => $snapshot['completed_exams'],
            'total_exams'          => $snapshot['total_exams'],
            'completed_lesson_ids' => $completedLessonIds,
            'watch_positions'      => $watchPositions,
        ]);
    }
}
