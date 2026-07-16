<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // day: 0=Sunday 1=Monday 2=Tuesday 3=Wednesday 4=Thursday
    public function up(): void
    {
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedTinyInteger('day');          // 0-4
            $table->unsignedTinyInteger('period_number');
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['class_id', 'day', 'period_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
