<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardNumber extends Model
{
    protected $fillable = [
        'card_id',
        'assigned_user_id',
        'number',
        'activate',
        'status',
        'sell',
    ];

    protected $casts = [
        'activate' => 'integer',
        'status'   => 'integer',
        'sell'     => 'integer',
    ];

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(Student::class, 'assigned_user_id');
    }

    public function isActive(): bool
    {
        return $this->activate === 1;
    }
    public function isUsed(): bool
    {
        return $this->status === 1;
    }
    public function isSold(): bool
    {
        return $this->sell === 1;
    }
}
