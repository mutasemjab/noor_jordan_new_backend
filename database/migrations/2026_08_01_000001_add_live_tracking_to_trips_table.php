<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started')->after('is_active');
            $table->timestamp('started_at')->nullable()->after('status');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->decimal('current_lat', 10, 7)->nullable()->after('completed_at');
            $table->decimal('current_lng', 10, 7)->nullable()->after('current_lat');
            $table->timestamp('last_location_at')->nullable()->after('current_lng');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['status', 'started_at', 'completed_at', 'current_lat', 'current_lng', 'last_location_at']);
        });
    }
};
