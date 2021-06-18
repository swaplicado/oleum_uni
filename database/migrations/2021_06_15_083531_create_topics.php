<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_topics', function (Blueprint $table) {
            $table->increments('id_topic');
            $table->string('topic', 100);
            $table->string('course_key', 100);
            $table->string('hash_id', 100);
            $table->boolean('is_deleted');
            $table->integer('course_id')->unsigned();
            $table->integer('sequence_id')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('course_id')->references('id_course')->on('uni_courses')->onDelete('cascade');
            $table->foreign('sequence_id')->references('id_sequence')->on('sys_sequences')->onDelete('cascade');
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
        Schema::dropIfExists('uni_topics');
    }
}
