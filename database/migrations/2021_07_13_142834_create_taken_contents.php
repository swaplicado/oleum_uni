<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTakenContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_taken_contents', function (Blueprint $table) {	
            $table->bigIncrements('id_content_taken');
            $table->dateTime('dtt_take');
            $table->dateTime('dtt_end')->nullable();
            $table->boolean('is_deleted');
            $table->bigInteger('take_control_id')->unsigned();
            $table->bigInteger('content_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('take_control_id')->references('id_taken_control')->on('uni_taken_controls')->onDelete('cascade');
            $table->foreign('content_id')->references('id_content')->on('uni_edu_contents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uni_taken_contents');
    }
}
