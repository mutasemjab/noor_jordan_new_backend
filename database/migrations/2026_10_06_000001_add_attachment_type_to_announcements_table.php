<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // null/'image' = existing behavior (including all historical rows,
            // which are always real images); 'pdf' = the new attachment kind.
            $table->string('attachment_type', 10)->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('attachment_type');
        });
    }
};
