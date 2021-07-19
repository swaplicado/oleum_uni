<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTakenEvalQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_taken_questions', function (Blueprint $table) {	
            $table->bigIncrements('id_question_taken');
            $table->boolean('is_correct');
            $table->boolean('is_deleted');
            $table->bigInteger('take_control_id')->unsigned();
            $table->bigInteger('question_id')->unsigned();
            $table->bigInteger('answer_n_id')->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('take_control_id')->references('id_taken_control')->on('uni_taken_controls')->onDelete('cascade');
            $table->foreign('question_id')->references('id_question')->on('uni_questions')->onDelete('cascade');
            $table->foreign('answer_n_id')->references('id_answer')->on('uni_answers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uni_taken_questions');
    }
}
