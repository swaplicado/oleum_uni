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
        Schema::create('edu_contents', function (blueprint $table) {	
            $table->bigIncrements('id_content');
            $table->string('file_name', 200);
            $table->string('file_sys_name', 200);
            $table->string('file_path', 250);
            $table->enum('file_type', ['video', 'pdf', 'image', 'text', 'link', 'audio', 'file']);
            $table->boolean('is_deleted');
            $table->timestamps();
        });	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('edu_contents');
    }
}
