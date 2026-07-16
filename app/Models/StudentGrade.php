<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    protected $fillable = [
        'student_id', 'class_id', 'subject_id', 'teacher_id',
        'title', 'score', 'max_score', 'graded_at',
    ];

    protected $casts = [
        'graded_at' => 'date',
        'score'     => 'decimal:2',
        'max_score' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function getPercentageAttribute(): float
    {
        return $this->max_score > 0
            ? round(($this->score / $this->max_score) * 100, 1)
            : 0;
    }
}
