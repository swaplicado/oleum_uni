<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id_module');
            $table->string('module', 100);
            $table->string('hash_id', 100);
            $table->longText('description');
            $table->longText('objetives');
            $table->boolean('is_deleted');
            $table->integer('knowledge_area_id')->unsigned();
            $table->integer('elem_status_id')->unsigned();
            $table->integer('sequence_id')->unsigned();
            $table->timestamps();

            $table->foreign('knowledge_area_id')->references('id_knowledge_area')->on('knowledge_areas')->onDelete('cascade');
            $table->foreign('elem_status_id')->references('id_element_status')->on('sys_element_status')->onDelete('cascade');
            $table->foreign('sequence_id')->references('id_sequence')->on('sys_sequences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
