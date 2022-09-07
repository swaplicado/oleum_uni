<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAreaIdToUniAssignmentsControl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uni_assignments_control', function (Blueprint $table) {
            $table->bigInteger('area_n_id')->unsigned()->nullable()->after('department_n_id');

            $table->foreign('area_n_id')->references('id_area')->on('adm_areas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uni_assignments_control', function (Blueprint $table) {
            $table->dropForeign(['area_n_id']);
            $table->dropColumn('area_n_id');
        });
    }
}
