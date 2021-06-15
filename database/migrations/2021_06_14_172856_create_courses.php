<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id_course');
            $table->string('course', 100);
            $table->string('hash_id', 100);
            $table->longText('description');
            $table->longText('objetives');
            $table->integer('completion_days')->unsigned();
            $table->integer('university_points');
            $table->boolean('is_deleted');
            $table->integer('module_id')->unsigned();
            $table->integer('elem_status_id')->unsigned();
            $table->integer('sequence_id')->unsigned();
            $table->timestamps();

            $table->foreign('module_id')->references('id_module')->on('modules')->onDelete('cascade');
            $table->foreign('elem_status_id')->references('id_element_status')->on('sys_element_status')->onDelete('cascade');
            $table->foreign('sequence_id')->references('id_sequence')->on('sys_sequences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
