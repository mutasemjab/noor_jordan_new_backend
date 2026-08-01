<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('home_lat', 10, 7)->nullable()->after('class_id');
            $table->decimal('home_lng', 10, 7)->nullable()->after('home_lat');
            $table->enum('transport_to_school', ['walk', 'bus'])->default('walk')->after('home_lng');
            $table->enum('transport_from_school', ['walk', 'bus'])->default('walk')->after('transport_to_school');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['home_lat', 'home_lng', 'transport_to_school', 'transport_from_school']);
        });
    }
};
