<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('educational_note_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educational_note_id')->nullable()->constrained('educational_notes')->nullOnDelete();
            $table->enum('actor_type', ['teacher', 'admin']);
            // Plain id (no FK) - the actor may later be hard-deleted (teachers/students
            // are real hard-deletes now) and the log must survive that regardless.
            $table->unsignedBigInteger('actor_id');
            $table->string('actor_name');
            $table->enum('action', ['created', 'updated', 'deleted']);
            // Denormalized snapshot of the note's class/teacher at the time of the
            // action, so filtering/grouping by class or teacher still works even
            // after the note itself (or its teacher) is gone.
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            // The note's own date at the time of this action (its new date for
            // created/updated, its last date for deleted) - kept as a real,
            // filterable column since the whole point of this log is catching
            // someone quietly changing a note's date.
            $table->date('note_date')->nullable();
            $table->json('changes');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['actor_type', 'class_id']);
            $table->index('note_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('educational_note_logs');
    }
};
