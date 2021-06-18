<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_assignments', function (Blueprint $table) {	
            $table->bigIncrements('id_assignment');
            $table->boolean('is_deleted');
            $table->date('dt_assignment');
            $table->date('dt_end');
            $table->boolean('is_over');
            $table->integer('knowledge_area_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->bigInteger('control_id')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('knowledge_area_id')->references('id_knowledge_area')->on('uni_knowledge_areas')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('control_id')->references('id_control')->on('uni_assignments_control')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('cascade');
        });	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uni_assignments');
    }
}
