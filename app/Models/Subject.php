<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name_ar', 'name_en',
        'icon', 'color_class', 'is_elective',
        'order_index', 'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_elective' => 'boolean',
    ];

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? ($this->name_ar ?? $this->name_en)
            : ($this->name_en ?? $this->name_ar);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects');
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subjects')
                    ->withPivot('teacher_id')
                    ->withTimestamps();
    }

    public function getGradesLabelAttribute(): string
    {
        return $this->classes->pluck('name')->implode('، ');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order_index');
    }
}
