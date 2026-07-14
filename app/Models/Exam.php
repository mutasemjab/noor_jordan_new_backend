<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'unit_id', 'lesson_id', 'subject_id',
        'title_ar', 'title_en',
        'description_ar', 'description_en',
        'exam_type', 'total_questions', 'duration_minutes',
        'total_marks', 'pass_marks', 'difficulty_level',
        'average_success_rate', 'total_attempts',
        'is_published', 'shuffle_questions', 'shuffle_options', 'show_result_immediately',
    ];

    protected $casts = [
        'is_published'            => 'boolean',
        'shuffle_questions'       => 'boolean',
        'shuffle_options'         => 'boolean',
        'show_result_immediately' => 'boolean',
    ];

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? ($this->title_ar ?? $this->title_en)
            : ($this->title_en ?? $this->title_ar);
    }

    public function getDescriptionAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? ($this->description_ar ?? $this->description_en ?? '')
            : ($this->description_en ?? $this->description_ar ?? '');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order_index');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
