<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentLoginAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = ['national_id', 'ip_address', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $attempt) {
            $attempt->created_at ??= now();
        });
    }
}
