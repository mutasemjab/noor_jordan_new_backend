<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'exam_id',
        'question_ar', 'question_en', 'image',
        'explanation_ar', 'explanation_en',
        'question_type', 'difficulty', 'marks', 'order_index',
    ];

    public function getQuestionTextAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? ($this->question_ar ?? $this->question_en ?? '')
            : ($this->question_en ?? $this->question_ar ?? '');
    }

    public function getExplanationAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? ($this->explanation_ar ?? $this->explanation_en ?? '')
            : ($this->explanation_en ?? $this->explanation_ar ?? '');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order_index');
    }

    public function correctOption()
    {
        return $this->hasOne(QuestionOption::class)->where('is_correct', true);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }
}
