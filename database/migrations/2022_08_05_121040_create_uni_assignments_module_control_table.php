<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniAssignmentsModuleControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_assignments_module_control', function (Blueprint $table) {
            $table->bigIncrements('id_module_control');
            $table->bigInteger('assignment_id')->unsigned();
            $table->date('dt_open');
            $table->date('dt_close');
            $table->integer('module_n_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->boolean('is_deleted')->default(0);
            $table->bigInteger('created_by')->unsigned();
            $table->bigInteger('updated_by')->unsigned();
            $table->timestamps();

            $table->foreign('assignment_id')->references('id_assignment')->on('uni_assignments')->onDelete('cascade');
            $table->foreign('module_n_id')->references('id_module')->on('uni_modules')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uni_assignments_module_control');
    }
}
