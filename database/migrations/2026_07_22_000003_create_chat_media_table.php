<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_media', function (Blueprint $table) {
            $table->id();
            $table->string('uploader_type'); // student | teacher
            $table->unsignedBigInteger('uploader_id');
            $table->string('path');
            $table->string('type'); // image | voice
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->timestamps();

            $table->index(['uploader_type', 'uploader_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_media');
    }
};
