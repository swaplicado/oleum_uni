<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreCourseIdToUniCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uni_courses', function (Blueprint $table) {
            $table->bigInteger('pre_course_id')->nullable()->after('completion_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uni_courses', function (Blueprint $table) {
            $table->dropColumn('pre_course_id');
        });
    }
}
