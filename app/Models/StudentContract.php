<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentContract extends Model
{
    protected $fillable = [
        'student_id', 'total_amount', 'contract_pdf', 'start_date', 'notes',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function payments()
    {
        return $this->hasMany(StudentPayment::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) $this->total_amount - $this->paid_amount;
    }
}
