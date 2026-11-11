<?php

namespace App\Observers;

use App\Models\ClassSubjectVideo;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\ExternalLink;
use App\Models\PreviousYearExam;
use App\Models\QuestionBank;
use App\Models\Worksheet;
use App\Services\StudentNotifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Notifies the target class whenever new content for students is added —
 * whether it came from the admin panel or from a teacher in the app, since
 * both go through the same Eloquent models.
 *
 * Content with a visibility flag (status / is_published) only notifies once
 * it is actually visible: on create if it's already visible, or later when
 * it is switched from hidden to visible.
 *
 * The educational notebook (EducationalNote / المفكرة) is intentionally NOT
 * observed: the school doesn't want notifications for it.
 *
 * Each `type` below must have a matching case in the Flutter app's
 * NotificationRouter so a tap opens the right page.
 */
class StudentContentObserver
{
    /** Fire only after the surrounding DB transaction (if any) commits. */
    public $afterCommit = true;

    /** Model class => visibility column (null = always visible). */
    protected const VISIBILITY = [
        ExamSchedule::class      => null,
        Worksheet::class         => 'status',
        Exam::class              => 'is_published',
        ExternalLink::class      => 'status',
        ClassSubjectVideo::class => null,
        PreviousYearExam::class  => 'status',
        QuestionBank::class      => 'status',
    ];

    public function created(Model $model): void
    {
        $flag = self::VISIBILITY[get_class($model)] ?? null;

        if ($flag && ! $model->{$flag}) {
            return; // saved hidden/draft — notify when it gets published
        }

        $this->notify($model);
    }

    public function updated(Model $model): void
    {
        $flag = self::VISIBILITY[get_class($model)] ?? null;

        if ($flag && $model->wasChanged($flag) && $model->{$flag} && ! $model->getOriginal($flag)) {
            $this->notify($model);
        }
    }

    protected function notify(Model $model): void
    {
        try {
            $message = $this->message($model);
            if (! $message) {
                return;
            }

            [$title, $body, $type] = $message;

            $subjectName = $this->subjectName($model);

            StudentNotifier::toClass(
                $model->class_id ? (int) $model->class_id : null,
                $title,
                $body,
                $type,
                array_filter([
                    'id'           => $model->getKey(),
                    'subject_id'   => $model->subject_id ?? null,
                    'subject_name' => $subjectName,
                ], fn ($v) => ! is_null($v) && $v !== '')
            );
        } catch (\Throwable $e) {
            Log::error('StudentContentObserver: ' . $e->getMessage());
        }
    }

    /** @return array{0:string,1:string,2:string}|null [title, body, type] */
    protected function message(Model $model): ?array
    {
        $subject = $this->subjectName($model);

        switch (true) {
            case $model instanceof ExamSchedule:
                return ['جدول امتحانات جديد', "تم نشر جدول الامتحانات: {$model->name}", 'exam_schedule'];

            case $model instanceof Worksheet:
                return ['ورقة عمل جديدة', $this->line($subject, $model->title_ar), 'worksheet'];

            case $model instanceof Exam:
                return ['امتحان جديد', $this->line($subject, $model->title_ar), 'exam'];

            case $model instanceof ExternalLink:
                return ['رابط جديد', $subject ? "تمت إضافة رابط جديد لمادة {$subject}" : 'تمت إضافة رابط جديد', 'external_link'];

            case $model instanceof ClassSubjectVideo:
                return ['فيديو جديد', $this->line($subject, $model->title), 'video'];

            case $model instanceof PreviousYearExam:
                return ['امتحان سنوات سابقة جديد', $this->line($subject, trim($model->title_ar . ' ' . $model->year)), 'previous_year_exam'];

            case $model instanceof QuestionBank:
                return ['بنك أسئلة جديد', $this->line($subject, $model->title_ar), 'question_bank'];
        }

        return null;
    }

    protected function subjectName(Model $model): ?string
    {
        if (empty($model->subject_id)) {
            return null;
        }

        return optional($model->subject)->name_ar;
    }

    protected function line(?string $subject, ?string $text): string
    {
        $text = trim((string) $text);

        if ($subject && $text) {
            return "{$subject}: {$text}";
        }

        return $subject ?: $text;
    }
}
