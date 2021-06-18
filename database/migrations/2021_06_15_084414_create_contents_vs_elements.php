<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsVsElements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_contents_vs_elements', function (Blueprint $table) {	
            $table->bigIncrements('id');
            $table->integer('order');
            $table->bigInteger('content_id')->unsigned();
            $table->integer('element_type_id')->unsigned();
            $table->integer('element_id')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->index(['content_id', 'element_type_id', 'element_id'], 'content_id_element_type_id_element_id');
            
            $table->foreign('content_id')->references('id_content')->on('uni_edu_contents')->onDelete('cascade');
            $table->foreign('element_type_id')->references('id_element_type')->on('sys_element_types')->onDelete('cascade');
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
        Schema::dropIfExists('uni_contents_vs_elements');
    }
}
