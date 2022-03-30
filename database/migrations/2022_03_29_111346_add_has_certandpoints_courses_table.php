<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasCertandpointsCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uni_courses', function (Blueprint $table) {
            $table->boolean('has_points')->after('completion_days');
            $table->boolean('has_document')->after('university_points')->default(true);
        });
        Schema::table('uni_modules', function (Blueprint $table) {
            $table->boolean('has_document')->after('objectives')->default(true);
        });
        Schema::table('uni_knowledge_areas', function (Blueprint $table) {
            $table->boolean('has_document')->after('objectives')->default(true);
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
            $table->dropColumn('has_points');
            $table->dropColumn('has_document');
        });
        Schema::table('uni_modules', function (Blueprint $table) {
            $table->dropColumn('has_document');
        });
        Schema::table('uni_knowledge_areas', function (Blueprint $table) {
            $table->dropColumn('has_document');
        });
    }
}
