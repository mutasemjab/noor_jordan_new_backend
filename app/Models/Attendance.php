<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['student_id', 'class_id', 'date', 'period', 'status', 'notes'];

    protected $casts = ['date' => 'date'];

    public static array $statuses = [
        'present' => 'حاضر',
        'absent'  => 'غائب',
        'late'    => 'متأخر',
        'excused' => 'غياب بعذر',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
