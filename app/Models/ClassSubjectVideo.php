<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSubjectVideo extends Model
{
    protected $fillable = ['class_id', 'subject_id', 'title', 'youtube_url', 'order_index'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getYoutubeIdAttribute(): string
    {
        preg_match('/(?:v=|youtu\.be\/|embed\/)([A-Za-z0-9_-]{11})/', $this->youtube_url, $m);
        return $m[1] ?? '';
    }

    public function getThumbnailAttribute(): string
    {
        $id = $this->youtube_id;
        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : '';
    }
}
