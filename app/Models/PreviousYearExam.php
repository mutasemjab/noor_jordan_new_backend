<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreviousYearExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'year',
        'subject_id',
        'title_ar',
        'title_en',
        'pdf_file',
        'pages',
        'file_size',
        'sort_order',
        'status',
    ];

    protected $appends = ['title'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getTitleAttribute()
    {
        return app()->getLocale() == 'ar'
            ? $this->title_ar
            : $this->title_en;
    }
}
