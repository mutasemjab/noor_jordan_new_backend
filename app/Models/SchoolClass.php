<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = ['name', 'is_active', 'homeroom_teacher_id'];

    protected $casts = ['is_active' => 'boolean'];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function educationalNotes()
    {
        return $this->hasMany(EducationalNote::class, 'class_id');
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects')
                    ->withPivot('teacher_id')
                    ->withTimestamps();
    }
}
