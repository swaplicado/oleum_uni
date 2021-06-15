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
        Schema::create('contents_vs_elements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('content_id')->unsigned();
            $table->integer('element_type_id')->unsigned();
            $table->integer('element_id')->unsigned();
            $table->timestamps();

            $table->index(['content_id', 'element_type_id', 'element_id']);

            $table->foreign('content_id')->references('id_content')->on('edu_contents')->onDelete('cascade');
            $table->foreign('element_type_id')->references('id_element_type')->on('sys_element_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents_vs_elements');
    }
}
