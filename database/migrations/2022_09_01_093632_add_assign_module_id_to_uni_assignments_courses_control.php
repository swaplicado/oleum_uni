<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssignModuleIdToUniAssignmentsCoursesControl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uni_assignments_courses_control', function (Blueprint $table) {
            $table->bigInteger('assignment_module_id')->unsigned()->nullable()->after('assignment_id');

            $table->foreign('assignment_module_id')->references('id_module_control')->on('uni_assignments_module_control');
        });

        $lModuleAssignmens = \DB::table('uni_assignments_module_control as amc')
                                ->get();

        foreach($lModuleAssignmens as $massign){
            $course = \DB::table('uni_assignments_courses_control as acc')
                        ->where('acc.assignment_id', $massign->assignment_id)
                        ->where('acc.module_n_id', $massign->module_n_id)
                        ->update(['assignment_module_id' => $massign->id_module_control]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uni_assignments_courses_control', function (Blueprint $table) {
            $table->dropForeign('assignment_module_id');
        });
    }
}
