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
        Schema::create('uni_questions', function (Blueprint $table) {	
            $table->bigIncrements('id_question');
            $table->longText('question');
            $table->integer('number_answers')->unsigned();
            $table->longText('answer_feedback');
            $table->boolean('is_deleted');
            $table->bigInteger('answer_id')->unsigned();
            $table->integer('subtopic_id')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
        //	$table->foreign('answer_id')->references('id_answer')->on('uni_answers')->onDelete('cascade');
            $table->foreign('subtopic_id')->references('id_subtopic')->on('uni_subtopics')->onDelete('cascade');
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
        Schema::dropIfExists('uni_questions');
    }
}
