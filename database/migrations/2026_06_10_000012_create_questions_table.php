<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->text('question_ar');
            $table->text('question_en')->nullable();
            $table->string('image')->nullable();
            $table->enum('question_type', ['mcq', 'true_false', 'short_answer'])->default('mcq');
            $table->text('explanation_ar')->nullable();
            $table->text('explanation_en')->nullable();
            $table->unsignedTinyInteger('marks')->default(1);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->unsignedSmallInteger('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
