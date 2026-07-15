<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Teacher extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guard = 'teacher';

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'avatar',
        'gender', 'nationality',
        'total_students',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
    ];

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function homeroomClasses()
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }
}
