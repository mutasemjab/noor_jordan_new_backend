<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    protected $fillable = ['name', 'class_id', 'image'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('assets/uploads/exam-schedules/' . $this->image) : null;
    }
}
