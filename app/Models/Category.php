<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'parent_id', 'level',
        'name_ar', 'name_en',
        'icon', 'image', 'order_index', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Accessors ─────────────────────────────────────────────────────────

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? ($this->name_ar ?? $this->name_en)
            : ($this->name_en ?? $this->name_ar);
    }

    public function getFullPathAttribute(): string
    {
        if (!$this->parent_id) {
            return $this->name;
        }
        return ($this->relationLoaded('parent') && $this->parent
            ? $this->parent->full_path . ' › '
            : '') . $this->name;
    }

    // ── Tree relationships ─────────────────────────────────────────────────

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order_index');
    }

    // Eager-loadable recursive children (use with caution on deep trees)
    public function allChildren()
    {
        return $this->children()->with(['allChildren', 'subjects' => fn ($q) => $q->orderBy('order_index')]);
    }

    // ── Other relationships ────────────────────────────────────────────────

    public function subjects()
    {
        return $this->hasMany(Subject::class)->orderBy('order_index');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order_index');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
