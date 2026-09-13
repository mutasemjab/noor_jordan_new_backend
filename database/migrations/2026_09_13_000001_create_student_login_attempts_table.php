<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('national_id');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('national_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_login_attempts');
    }
};
