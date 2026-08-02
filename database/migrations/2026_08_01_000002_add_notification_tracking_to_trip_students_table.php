<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trip_students', function (Blueprint $table) {
            // "قربنا" push already sent for this stop, this run of the trip.
            $table->timestamp('notified_at')->nullable()->after('eta_minutes');
            // Stop considered passed (auto-detected on approach, or set manually
            // by the companion) — used to move on to the next stop's notification.
            $table->timestamp('arrived_at')->nullable()->after('notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('trip_students', function (Blueprint $table) {
            $table->dropColumn(['notified_at', 'arrived_at']);
        });
    }
};
