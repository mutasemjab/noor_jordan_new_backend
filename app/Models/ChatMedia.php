<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMedia extends Model
{
    protected $table = 'chat_media';

    protected $fillable = [
        'uploader_type', 'uploader_id',
        'path', 'type', 'duration_seconds', 'size_bytes',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'size_bytes'       => 'integer',
    ];

    public function getUrlAttribute(): string
    {
        return asset('assets/uploads/chat/' . $this->path);
    }
}
