<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'category_id',
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

    public function getFullPathAttribute(): string
    {
        if ($this->relationLoaded('category') && $this->category) {
            return $this->category->full_path . ' › ' . $this->name;
        }
        return $this->name;
    }

    // ── Relationships ─────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order_index');
    }
}
