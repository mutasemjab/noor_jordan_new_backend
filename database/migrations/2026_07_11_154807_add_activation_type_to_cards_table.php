<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cards', function (Blueprint $table) {
            // course  → activates one specific course
            // teacher → activates all courses by one teacher
            // price   → activates any course whose price equals this card's selling_price
            $table->enum('activation_type', ['course', 'teacher', 'price'])->default('price')->after('selling_price');
            $table->unsignedBigInteger('linked_course_id')->nullable()->after('activation_type');
            $table->unsignedBigInteger('linked_teacher_id')->nullable()->after('linked_course_id');

            $table->foreign('linked_course_id')->references('id')->on('courses')->nullOnDelete();
            $table->foreign('linked_teacher_id')->references('id')->on('teachers')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropForeign(['linked_course_id']);
            $table->dropForeign(['linked_teacher_id']);
            $table->dropColumn(['activation_type', 'linked_course_id', 'linked_teacher_id']);
        });
    }
};
