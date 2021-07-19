<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CretePrerequisites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_prerequisites', function (Blueprint $table) {	
            $table->bigIncrements('id_prerequisite');
            $table->boolean('is_deleted');
            $table->integer('element_type_id')->unsigned();
            $table->integer('knowledge_area_n_id')->unsigned()->nullable();
            $table->integer('module_n_id')->unsigned()->nullable();
            $table->integer('course_n_id')->unsigned()->nullable();
            $table->integer('topic_n_id')->unsigned()->nullable();
            $table->integer('subtopic_n_id')->unsigned()->nullable();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('element_type_id')->references('id_element_type')->on('sys_element_types')->onDelete('cascade');
            $table->foreign('knowledge_area_n_id')->references('id_knowledge_area')->on('uni_knowledge_areas')->onDelete('cascade');
            $table->foreign('module_n_id')->references('id_module')->on('uni_modules')->onDelete('cascade');
            $table->foreign('course_n_id')->references('id_course')->on('uni_courses')->onDelete('cascade');
            $table->foreign('topic_n_id')->references('id_topic')->on('uni_topics')->onDelete('cascade');
            $table->foreign('subtopic_n_id')->references('id_subtopic')->on('uni_subtopics')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('uni_prerequisites_rows', function (Blueprint $table) {	
            $table->bigIncrements('id');
            $table->boolean('is_deleted');
            $table->bigInteger('prerequisite_id')->unsigned();
            $table->integer('element_type_id')->unsigned();
            $table->integer('knowledge_area_n_id')->unsigned()->nullable();
            $table->integer('module_n_id')->unsigned()->nullable();
            $table->integer('course_n_id')->unsigned()->nullable();
            $table->integer('topic_n_id')->unsigned()->nullable();
            $table->integer('subtopic_n_id')->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('prerequisite_id')->references('id_prerequisite')->on('uni_prerequisites')->onDelete('cascade');
            $table->foreign('element_type_id')->references('id_element_type')->on('sys_element_types')->onDelete('cascade');
            $table->foreign('knowledge_area_n_id')->references('id_knowledge_area')->on('uni_knowledge_areas')->onDelete('cascade');
            $table->foreign('module_n_id')->references('id_module')->on('uni_modules')->onDelete('cascade');
            $table->foreign('course_n_id')->references('id_course')->on('uni_courses')->onDelete('cascade');
            $table->foreign('topic_n_id')->references('id_topic')->on('uni_topics')->onDelete('cascade');
            $table->foreign('subtopic_n_id')->references('id_subtopic')->on('uni_subtopics')->onDelete('cascade');
        });	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uni_prerequisites_rows');
        Schema::dropIfExists('uni_prerequisites');
    }
}
