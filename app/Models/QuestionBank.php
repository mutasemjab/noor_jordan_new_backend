<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'title_ar',
        'title_en',
        'tag_ar',
        'tag_en',
        'pdf_file',
        'pages',
        'file_size',
        'sort_order',
        'status',
    ];

    protected $appends = [
        'title',
        'tag'
    ];

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

    public function getTagAttribute()
    {
        return app()->getLocale() == 'ar'
            ? $this->tag_ar
            : $this->tag_en;
    }

}
