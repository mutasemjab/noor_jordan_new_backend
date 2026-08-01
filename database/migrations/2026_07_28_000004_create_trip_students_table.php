<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('stop_order')->default(0);
            $table->unsignedInteger('eta_minutes')->nullable(); // minutes from trip start to this stop
            $table->timestamps();

            $table->unique(['trip_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_students');
    }
};
