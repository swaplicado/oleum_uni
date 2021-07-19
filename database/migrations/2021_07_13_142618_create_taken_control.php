<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTakenControl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_taken_controls', function (Blueprint $table) {	
            $table->bigIncrements('id_taken_control');
            $table->dateTime('dtt_take');
            $table->dateTime('dtt_end')->nullable();
            $table->decimal('min_grade', 8,2);
            $table->decimal('grade', 8,2);
            $table->integer('university_points');
            $table->integer('num_questions');
            $table->boolean('is_evaluation');
            $table->boolean('is_deleted');
            $table->integer('element_type_id')->unsigned();
            $table->integer('knowledge_n_area_id')->unsigned()->nullable();
            $table->integer('module_n_id')->unsigned()->nullable();
            $table->integer('course_n_id')->unsigned()->nullable();
            $table->integer('topic_n_id')->unsigned()->nullable();
            $table->integer('subtopic_n_id')->unsigned()->nullable();
            $table->integer('student_id')->unsigned();
            $table->integer('status_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('element_type_id')->references('id_element_type')->on('sys_element_types')->onDelete('cascade');
            $table->foreign('knowledge_n_area_id')->references('id_knowledge_area')->on('uni_knowledge_areas')->onDelete('cascade');
            $table->foreign('module_n_id')->references('id_module')->on('uni_modules')->onDelete('cascade');
            $table->foreign('course_n_id')->references('id_course')->on('uni_courses')->onDelete('cascade');
            $table->foreign('topic_n_id')->references('id_topic')->on('uni_topics')->onDelete('cascade');
            $table->foreign('subtopic_n_id')->references('id_subtopic')->on('uni_subtopics')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('status_id')->references('id_status')->on('sys_take_status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uni_taken_controls');
    }
}
