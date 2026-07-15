<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $guard = 'student';

    protected $fillable = [
        'name', 'email', 'phone', 'national_id', 'fcm_token', 'password', 'avatar',
        'date_of_birth', 'nationality', 'gender',
        'class_id', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth'     => 'date',
        'is_active'         => 'boolean',
    ];

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function contract()
    {
        return $this->hasOne(StudentContract::class);
    }

    public function siblings()
    {
        return $this->belongsToMany(
            Student::class,
            'student_siblings',
            'student_id',
            'sibling_id'
        )->withoutTrashed();
    }

    public function syncSiblings(array $siblingIds): void
    {
        // Remove all existing pairs involving this student (both directions)
        DB::table('student_siblings')
            ->where('student_id', $this->id)
            ->orWhere('sibling_id', $this->id)
            ->delete();

        $rows = [];
        foreach (array_unique($siblingIds) as $siblingId) {
            if ((int) $siblingId === $this->id) continue;
            $rows[] = ['student_id' => $this->id,    'sibling_id' => $siblingId];
            $rows[] = ['student_id' => $siblingId,   'sibling_id' => $this->id];
        }

        if ($rows) {
            DB::table('student_siblings')->insertOrIgnore($rows);
        }
    }
}
