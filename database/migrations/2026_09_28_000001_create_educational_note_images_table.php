<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('educational_note_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educational_note_id')->constrained('educational_notes')->cascadeOnDelete();
            $table->string('image');
            $table->unsignedTinyInteger('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('educational_note_images');
    }
};
