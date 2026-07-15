<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    protected $fillable = [
        'student_contract_id', 'receipt_number', 'amount', 'paid_at', 'notes',
    ];

    protected $casts = [
        'paid_at' => 'date',
        'amount'  => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(StudentContract::class, 'student_contract_id');
    }

    public static function generateReceiptNumber(): string
    {
        $year   = now()->format('Y');
        $last   = static::whereYear('created_at', $year)->max('id') ?? 0;
        return 'REC-' . $year . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}
