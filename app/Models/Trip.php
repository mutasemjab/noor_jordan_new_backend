<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'bus_id', 'companion_teacher_id', 'type', 'sequence_number', 'name',
        'total_distance_km', 'total_duration_minutes', 'google_maps_url', 'is_active',
        'status', 'started_at', 'completed_at', 'current_lat', 'current_lng', 'last_location_at',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'started_at'       => 'datetime',
        'completed_at'     => 'datetime',
        'current_lat'      => 'decimal:7',
        'current_lng'      => 'decimal:7',
        'last_location_at' => 'datetime',
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
                    ->withPivot('stop_order', 'eta_minutes', 'notified_at', 'arrived_at')
                    ->withTimestamps()
                    ->orderBy('trip_students.stop_order');
    }

    /**
     * The first not-yet-arrived stop, in route order — the one location
     * updates should be measured against for the "approaching" notification.
     */
    public function nextStop(): ?Student
    {
        return $this->students->first(fn (Student $s) => is_null($s->pivot->arrived_at));
    }
}
