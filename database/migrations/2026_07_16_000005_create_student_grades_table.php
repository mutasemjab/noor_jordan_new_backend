<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 200);       // e.g. "اختبار منتصف الفصل"
            $table->decimal('score', 6, 2);
            $table->decimal('max_score', 6, 2)->default(100);
            $table->date('graded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};
