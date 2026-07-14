<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LessonProgress;

class Lesson extends Model
{
    protected $fillable = [
        'unit_id',
        'title_ar', 'title_en',
        'lesson_type',
        'video_url', 'file_path',
        'duration_minutes', 'order_index', 'is_free', 'is_published',
    ];

    protected $casts = [
        'is_free'      => 'boolean',
        'is_published' => 'boolean',
    ];

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? ($this->title_ar ?? $this->title_en)
            : ($this->title_en ?? $this->title_ar);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, Unit::class, 'id', 'id', 'unit_id', 'course_id');
    }

    public function studentProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }
}
