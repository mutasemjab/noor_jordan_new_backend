<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'course_id',
        'title_ar', 'title_en',
        'description_ar', 'description_en',
        'order_index', 'total_videos', 'total_pdfs', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
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

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order_index');
    }

    public function materials()
    {
        return $this->hasMany(Material::class)->orderBy('order_index');
    }

    public function exam()
    {
        return $this->hasOne(Exam::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
