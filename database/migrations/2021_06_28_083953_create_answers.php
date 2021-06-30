<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_answers', function (Blueprint $table) {	
            $table->bigIncrements('id_answer');
            $table->string('answer', 200);
            $table->boolean('is_deleted');
            $table->bigInteger('content_n_id')->unsigned()->nullable();
            $table->bigInteger('question_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('content_n_id')->references('id_content')->on('uni_edu_contents')->onDelete('cascade');
            $table->foreign('question_id')->references('id_question')->on('uni_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uni_answers');
    }
}
