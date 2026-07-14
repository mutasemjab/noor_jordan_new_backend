<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalNote extends Model
{
    protected $fillable = [
        'teacher_id', 'class_id',
        'type', 'title', 'description', 'attachment', 'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
