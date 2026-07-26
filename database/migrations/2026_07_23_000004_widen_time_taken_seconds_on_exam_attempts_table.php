<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Existing in-progress attempts have NULL here; backfill before adding NOT NULL.
        DB::table('exam_attempts')->whereNull('time_taken_seconds')->update(['time_taken_seconds' => 0]);

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->unsignedInteger('time_taken_seconds')->default(0)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->unsignedSmallInteger('time_taken_seconds')->nullable()->change();
        });
    }
};
