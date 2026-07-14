<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $fillable = [
        'question_id',
        'option_text_ar', 'option_text_en',
        'image', 'is_correct', 'order_index',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function getOptionTextAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? ($this->option_text_ar ?? $this->option_text_en ?? '')
            : ($this->option_text_en ?? $this->option_text_ar ?? '');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
