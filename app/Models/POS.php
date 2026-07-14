<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class POS extends Model
{
    protected $table = 'p_o_s';

    protected $fillable = ['city_id', 'name_en', 'name_ar', 'phone', 'google_map_link'];

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar'
            ? ($this->name_ar ?? $this->name_en)
            : ($this->name_en ?? $this->name_ar);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'pos_id');
    }
}
