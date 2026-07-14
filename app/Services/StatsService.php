<?php

namespace App\Services;

use App\Models\{Category, ContactMessage, Course, Enrollment, ExamAttempt, Order, Student, Teacher};

class StatsService
{
    public function adminStats(): array
    {
        $lastMonth = now()->subMonth();

        return [
            'total_students'      => Student::count(),
            'total_teachers'      => Teacher::where('is_active', true)->count(),
            'total_courses'       => Course::where('is_published', true)->count(),
            'courses_completed'   => Enrollment::where('is_completed', true)->count(),
            'avg_rating'          => Course::where('is_published', true)->avg('average_rating') ?? 0,
            'total_enrollments'   => Enrollment::count(),
            'unread_messages'     => ContactMessage::where('status', 'new')->count(),
        ];
    }

    public function teacherStats(int $teacherId): array
    {
        $courseIds = Course::where('teacher_id', $teacherId)->pluck('id');

        return [
            'total_courses'    => $courseIds->count(),
            'total_students'   => Enrollment::whereIn('course_id', $courseIds)
                                    ->where('is_active', true)
                                    ->distinct('student_id')
                                    ->count('student_id'),
            'avg_rating'       => round(Course::whereIn('id', $courseIds)->avg('average_rating') ?? 0, 1),
        ];
    }

    public function studentStats(int $studentId): array
    {
        $enrollments = Enrollment::where('student_id', $studentId);

        return [
            'enrolled_courses'  => $enrollments->count(),
            'completed_courses' => (clone $enrollments)->where('is_completed', true)->count(),
            'avg_progress'      => round((clone $enrollments)->avg('progress_percentage') ?? 0),
            'exam_attempts'     => ExamAttempt::where('student_id', $studentId)
                                    ->where('status', 'submitted')
                                    ->count(),
        ];
    }
}
