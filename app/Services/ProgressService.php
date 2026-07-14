<?php

namespace App\Services;

use App\Models\{Course, Enrollment, ExamAttempt, LessonProgress};

class ProgressService
{
    /**
     * Pure calculation — no DB writes, no auth dependency.
     * Safe to call from any panel, API, queue job, etc.
     *
     * @return array{
     *   percentage: int,
     *   completed_lessons: int,
     *   total_lessons: int,
     *   completed_exams: int,
     *   total_exams: int,
     *   completed_items: int,
     *   total_items: int,
     * }
     */
    public function snapshot(int $studentId, int $courseId): array
    {
        $course = Course::with([
            'units.lessons' => fn ($q) => $q->where('is_published', true),
            'publishedExams',
        ])->findOrFail($courseId);

        $lessonIds = $course->units->flatMap(fn ($u) => $u->lessons->pluck('id'));
        $examIds   = $course->publishedExams->pluck('id');

        $totalLessons = $lessonIds->count();
        $totalExams   = $examIds->count();
        $totalItems   = $totalLessons + $totalExams;

        if ($totalItems === 0) {
            return $this->emptySnapshot();
        }

        $completedLessons = LessonProgress::where('student_id', $studentId)
            ->whereIn('lesson_id', $lessonIds)
            ->where('is_completed', true)
            ->count();

        $completedExams = $examIds->isNotEmpty()
            ? ExamAttempt::where('student_id', $studentId)
                ->whereIn('exam_id', $examIds)
                ->where('status', 'submitted')
                ->distinct('exam_id')
                ->count('exam_id')
            : 0;

        $completedItems = $completedLessons + $completedExams;
        $percentage     = (int) min(100, round(($completedItems / $totalItems) * 100));

        return [
            'percentage'        => $percentage,
            'completed_lessons' => $completedLessons,
            'total_lessons'     => $totalLessons,
            'completed_exams'   => $completedExams,
            'total_exams'       => $totalExams,
            'completed_items'   => $completedItems,
            'total_items'       => $totalItems,
        ];
    }

    /**
     * Calculate and persist to enrollments table.
     * Returns the new percentage.
     */
    public function recalculate(int $studentId, int $courseId): int
    {
        $enrollment = Enrollment::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $data = $this->snapshot($studentId, $courseId);

        $enrollment->update([
            'progress_percentage' => $data['percentage'],
            'is_completed'        => $data['percentage'] >= 100,
            'completed_at'        => $data['percentage'] >= 100
                ? ($enrollment->completed_at ?? now())
                : null,
        ]);

        return $data['percentage'];
    }

    /**
     * Recalculate progress for every enrolled student in a course.
     * Useful when a teacher adds/removes a lesson or exam.
     */
    public function recalculateForCourse(int $courseId): void
    {
        Enrollment::where('course_id', $courseId)
            ->where('is_active', true)
            ->select('student_id', 'course_id')
            ->each(fn ($e) => $this->recalculate($e->student_id, $e->course_id));
    }

    /**
     * Return the snapshot for every student enrolled in a course.
     * Useful for teacher/admin student-list pages.
     *
     * @return array<int, array> keyed by student_id
     */
    public function snapshotForCourse(int $courseId): array
    {
        $studentIds = Enrollment::where('course_id', $courseId)
            ->where('is_active', true)
            ->pluck('student_id');

        return $studentIds
            ->mapWithKeys(fn ($sid) => [$sid => $this->snapshot($sid, $courseId)])
            ->all();
    }

    private function emptySnapshot(): array
    {
        return [
            'percentage'        => 0,
            'completed_lessons' => 0,
            'total_lessons'     => 0,
            'completed_exams'   => 0,
            'total_exams'       => 0,
            'completed_items'   => 0,
            'total_items'       => 0,
        ];
    }
}
