<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalNoteImage extends Model
{
    protected $fillable = ['educational_note_id', 'image', 'order_index'];

    public function educationalNote()
    {
        return $this->belongsTo(EducationalNote::class);
    }
}
