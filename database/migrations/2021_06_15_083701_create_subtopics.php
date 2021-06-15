<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubtopics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subtopics', function (Blueprint $table) {
            $table->increments('id_subtopic');
            $table->string('subtopic', 100);
            $table->string('hash_id', 100);
            $table->integer('number_questions')->unsigned();
            $table->boolean('is_deleted');
            $table->integer('topic_id')->unsigned();
            $table->timestamps();

            $table->foreign('topic_id')->references('id_topic')->on('topics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subtopics');
    }
}
