<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalNoteLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'educational_note_id', 'actor_type', 'actor_id', 'actor_name',
        'action', 'class_id', 'teacher_id', 'note_date', 'changes',
    ];

    protected $casts = [
        'changes'    => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $log) {
            $log->created_at ??= now();
        });
    }

    public function educationalNote()
    {
        return $this->belongsTo(EducationalNote::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Snapshot of the fields worth auditing on a note, taken before/after a
     * write so record() can diff them (dates in particular - the whole point
     * of this log is catching someone quietly correcting a wrong date).
     */
    public static function snapshot(EducationalNote $note): array
    {
        return [
            'title'       => $note->title,
            'description' => $note->description,
            'type'        => $note->type,
            'date'        => $note->date?->format('Y-m-d'),
            'class_id'    => $note->class_id,
            'subject_id'  => $note->subject_id,
            'teacher_id'  => $note->teacher_id,
            'attachment'  => $note->attachment,
        ];
    }

    /**
     * Record one create/update/delete action against a note. $before is the
     * snapshot() taken prior to the write (required for 'updated'/'deleted');
     * for 'updated', only fields that actually changed are stored.
     */
    public static function record(
        string $actorType,
        int $actorId,
        string $actorName,
        string $action,
        EducationalNote $note,
        ?array $before = null
    ): void {
        $after = $action === 'deleted' ? [] : self::snapshot($note);
        $before = $before ?? [];

        $changes = [];
        if ($action === 'created') {
            foreach ($after as $field => $value) {
                $changes[$field] = ['old' => null, 'new' => $value];
            }
        } elseif ($action === 'deleted') {
            foreach ($before as $field => $value) {
                $changes[$field] = ['old' => $value, 'new' => null];
            }
        } else {
            foreach ($after as $field => $value) {
                if (($before[$field] ?? null) !== $value) {
                    $changes[$field] = ['old' => $before[$field] ?? null, 'new' => $value];
                }
            }
            if (empty($changes)) {
                return; // nothing actually changed - don't log noise
            }
        }

        static::create([
            'educational_note_id' => $note->id,
            'actor_type'          => $actorType,
            'actor_id'            => $actorId,
            'actor_name'          => $actorName,
            'action'              => $action,
            'class_id'            => $note->class_id,
            'teacher_id'          => $note->teacher_id,
            'note_date'           => $note->date?->format('Y-m-d'),
            'changes'             => $changes,
        ]);
    }
}
