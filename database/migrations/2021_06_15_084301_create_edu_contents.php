<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEduContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_edu_contents', function (Blueprint $table) {	
            $table->bigIncrements('id_content');
            $table->string('file_name', 200);
            $table->string('file_extension', 10);
            $table->string('file_sys_name', 200);
            $table->string('file_path', 250);
            $table->enum('file_type', ['video', 'pdf', 'image', 'audio', 'text', 'file', 'link']);
            $table->integer('file_size')->unsigned();
            $table->boolean('is_deleted');
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
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
        Schema::dropIfExists('uni_edu_contents');
    }
}
