<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'unit_id',
        'title_ar', 'title_en',
        'description_ar', 'description_en',
        'file_type', 'file_path', 'file_size_mb',
        'pages_count', 'download_count', 'is_downloadable', 'is_free', 'order_index',
    ];

    protected $casts = [
        'is_downloadable' => 'boolean',
        'is_free'         => 'boolean',
        'file_size_mb'    => 'decimal:2',
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
}
