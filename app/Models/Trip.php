<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'bus_id', 'companion_teacher_id', 'type', 'sequence_number', 'name',
        'total_distance_km', 'total_duration_minutes', 'google_maps_url', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function companionTeacher()
    {
        return $this->belongsTo(Teacher::class, 'companion_teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'trip_students')
                    ->withPivot('stop_order', 'eta_minutes')
                    ->withTimestamps()
                    ->orderBy('trip_students.stop_order');
    }
}
