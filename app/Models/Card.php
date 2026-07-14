<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'pos_id', 'name_en', 'name_ar', 'selling_price', 'number_of_cards', 'photo',
        'activation_type', 'linked_course_id', 'linked_teacher_id',
    ];

    protected $casts = [
        'selling_price'   => 'decimal:2',
        'number_of_cards' => 'decimal:0',
    ];

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar'
            ? ($this->name_ar ?? $this->name_en)
            : ($this->name_en ?? $this->name_ar);
    }

    public function pos()
    {
        return $this->belongsTo(POS::class, 'pos_id');
    }

    public function cardNumbers()
    {
        return $this->hasMany(CardNumber::class, 'card_id');
    }

    public function linkedCourse()
    {
        return $this->belongsTo(\App\Models\Course::class, 'linked_course_id');
    }

    public function linkedTeacher()
    {
        return $this->belongsTo(\App\Models\Teacher::class, 'linked_teacher_id');
    }
}
