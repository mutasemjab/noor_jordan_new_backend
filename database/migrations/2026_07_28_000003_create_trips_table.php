<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->foreignId('companion_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->enum('type', ['pickup', 'dropoff']); // pickup = morning arrival, dropoff = afternoon departure
            $table->unsignedTinyInteger('sequence_number')->default(1); // 2nd/3rd run of the same bus when demand exceeds capacity
            $table->string('name')->nullable();
            $table->decimal('total_distance_km', 8, 2)->nullable();
            $table->unsignedInteger('total_duration_minutes')->nullable();
            $table->string('google_maps_url', 2048)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
