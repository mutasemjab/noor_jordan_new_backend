<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = ['name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function educationalNotes()
    {
        return $this->hasMany(EducationalNote::class, 'class_id');
    }
}
