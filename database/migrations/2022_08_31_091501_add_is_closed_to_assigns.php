<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsClosedToAssigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uni_assignments', function (Blueprint $table) {
            $table->boolean('is_closed')->after('dt_end')->default(0);
        });

        Schema::table('uni_assignments_module_control', function (Blueprint $table) {
            $table->boolean('is_closed')->after('dt_close')->default(0);
        });

        Schema::table('uni_assignments_courses_control', function (Blueprint $table) {
            $table->boolean('is_closed')->after('dt_close')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uni_assignments', function (Blueprint $table) {
            $table->dropColumn('is_closed');
        });

        Schema::table('uni_assignments_module_control', function (Blueprint $table) {
            $table->dropColumn('is_closed');
        });

        Schema::table('uni_assignments_courses_control', function (Blueprint $table) {
            $table->dropColumn('is_closed');
        });
    }
}
