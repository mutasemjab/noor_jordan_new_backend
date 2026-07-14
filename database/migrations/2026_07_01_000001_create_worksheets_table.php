<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worksheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->string('tag_ar')->nullable();
            $table->string('tag_en')->nullable();
            $table->integer('year')->nullable();
            $table->integer('pages')->default(0);
            $table->decimal('file_size', 8, 2)->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('pdf_file');
            $table->boolean('status')->default(true);
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worksheets');
    }
};
