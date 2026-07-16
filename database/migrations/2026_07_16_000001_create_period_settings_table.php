<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('period_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('period_number')->unique();
            $table->string('label', 50);          // e.g. "الحصة الأولى"
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('period_settings');
    }
};
