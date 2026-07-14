<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worksheet extends Model
{
    protected $fillable = [
        'teacher_id', 'subject_id', 'title_ar', 'title_en',
        'tag_ar', 'tag_en',
        'year', 'pages', 'file_size', 'sort_order',
        'pdf_file', 'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
