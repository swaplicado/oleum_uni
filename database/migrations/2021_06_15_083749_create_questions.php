<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (blueprint $table) {	
            $table->bigIncrements('id_question');
            $table->longText('question');
            $table->integer('number_answers')->unsigned();
            $table->boolean('is_deleted');
            $table->bigInteger('answer_id')->unsigned();
            $table->integer('subtopic')->unsigned();
            $table->timestamps();
            
        //	$table->foreign('answer_id')->references('id_answer')->on('answers')->onDelete('cascade');
            $table->foreign('subtopic')->references('id_subtopic')->on('subtopics')->onDelete('cascade');
        });	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
