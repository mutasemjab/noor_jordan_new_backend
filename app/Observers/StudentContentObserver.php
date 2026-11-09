<?php

namespace App\Observers;

use App\Models\ClassSubjectVideo;
use App\Models\EducationalNote;
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
        EducationalNote::class   => null,
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

            [$title, $body] = $message;

            StudentNotifier::toClass(
                $model->class_id ? (int) $model->class_id : null,
                $title,
                $body
            );
        } catch (\Throwable $e) {
            Log::error('StudentContentObserver: ' . $e->getMessage());
        }
    }

    /** @return array{0:string,1:string}|null [title, body] */
    protected function message(Model $model): ?array
    {
        $subject = $this->subjectName($model);

        switch (true) {
            case $model instanceof ExamSchedule:
                return ['جدول امتحانات جديد', "تم نشر جدول الامتحانات: {$model->name}"];

            case $model instanceof Worksheet:
                return ['ورقة عمل جديدة', $this->line($subject, $model->title_ar)];

            case $model instanceof Exam:
                return ['امتحان جديد', $this->line($subject, $model->title_ar)];

            case $model instanceof EducationalNote:
                $title = $model->type === 'homework' ? 'واجب جديد' : 'ملاحظة درس جديدة';
                return [$title, $this->line($subject, $model->title)];

            case $model instanceof ExternalLink:
                return ['رابط جديد', $subject ? "تمت إضافة رابط جديد لمادة {$subject}" : 'تمت إضافة رابط جديد'];

            case $model instanceof ClassSubjectVideo:
                return ['فيديو جديد', $this->line($subject, $model->title)];

            case $model instanceof PreviousYearExam:
                return ['امتحان سنوات سابقة جديد', $this->line($subject, trim($model->title_ar . ' ' . $model->year))];

            case $model instanceof QuestionBank:
                return ['بنك أسئلة جديد', $this->line($subject, $model->title_ar)];
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
