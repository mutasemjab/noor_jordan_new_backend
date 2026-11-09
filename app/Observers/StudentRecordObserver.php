<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\StudentGrade;
use App\Services\StudentNotifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Per-student notifications: a new/changed grade, or being marked
 * absent/late. Fires for entries made from the admin panel or the teacher app.
 */
class StudentRecordObserver
{
    public $afterCommit = true;

    /** Attendance statuses worth notifying about ("present" is not news). */
    protected const ATTENDANCE_NOTIFY = ['absent', 'late', 'excused'];

    public function created(Model $model): void
    {
        if ($model instanceof StudentGrade) {
            $this->notifyGrade($model, 'علامة جديدة');
        } elseif ($model instanceof Attendance && in_array($model->status, self::ATTENDANCE_NOTIFY, true)) {
            $this->notifyAttendance($model);
        }
    }

    public function updated(Model $model): void
    {
        if ($model instanceof StudentGrade && ($model->wasChanged('score') || $model->wasChanged('max_score'))) {
            $this->notifyGrade($model, 'تم تعديل علامة');
        } elseif ($model instanceof Attendance && $model->wasChanged('status')
            && in_array($model->status, self::ATTENDANCE_NOTIFY, true)) {
            $this->notifyAttendance($model);
        }
    }

    protected function notifyGrade(StudentGrade $grade, string $title): void
    {
        try {
            $subject = optional($grade->subject)->name_ar;
            $score   = rtrim(rtrim(number_format((float) $grade->score, 2, '.', ''), '0'), '.');
            $max     = rtrim(rtrim(number_format((float) $grade->max_score, 2, '.', ''), '0'), '.');

            $body = trim(($subject ? "{$subject} - " : '') . "{$grade->title}: {$score} من {$max}");

            StudentNotifier::toStudent((int) $grade->student_id, $title, $body);
        } catch (\Throwable $e) {
            Log::error('StudentRecordObserver (grade): ' . $e->getMessage());
        }
    }

    protected function notifyAttendance(Attendance $attendance): void
    {
        try {
            $label = Attendance::$statuses[$attendance->status] ?? $attendance->status;
            $date  = optional($attendance->date)->format('Y-m-d');

            $body = "تم تسجيلك ({$label}) بتاريخ {$date}"
                . ($attendance->period ? " - الحصة {$attendance->period}" : '');

            StudentNotifier::toStudent((int) $attendance->student_id, 'تسجيل الحضور', $body);
        } catch (\Throwable $e) {
            Log::error('StudentRecordObserver (attendance): ' . $e->getMessage());
        }
    }
}
