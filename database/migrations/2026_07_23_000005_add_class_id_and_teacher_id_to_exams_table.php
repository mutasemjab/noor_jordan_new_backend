<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->after('subject_id')
                ->constrained('classes')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->after('class_id')
                ->constrained('teachers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_id');
            $table->dropConstrainedForeignId('teacher_id');
        });
    }
};
