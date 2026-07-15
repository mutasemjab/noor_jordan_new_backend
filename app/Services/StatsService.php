<?php

namespace App\Services;

use App\Models\{ContactMessage, ExamAttempt, Student, Teacher};

class StatsService
{
    public function adminStats(): array
    {
        return [
            'total_students'  => Student::count(),
            'total_teachers'  => Teacher::where('is_active', true)->count(),
            'unread_messages' => ContactMessage::where('status', 'new')->count(),
        ];
    }

    public function teacherStats(int $teacherId): array
    {
        return [
            'total_exams'   => \App\Models\Exam::where('subject_id', function ($q) use ($teacherId) {
                $q->select('subjects.id')
                  ->from('subjects')
                  ->join('teacher_subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
                  ->where('teacher_subjects.teacher_id', $teacherId);
            })->count(),
        ];
    }

    public function studentStats(int $studentId): array
    {
        return [
            'exam_attempts' => ExamAttempt::where('student_id', $studentId)
                                ->where('status', 'submitted')
                                ->count(),
        ];
    }
}
