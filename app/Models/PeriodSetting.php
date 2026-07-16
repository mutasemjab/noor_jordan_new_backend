<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodSetting extends Model
{
    protected $fillable = ['period_number', 'label', 'start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];
}
