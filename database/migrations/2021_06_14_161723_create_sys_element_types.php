<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysElementTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_element_types', function (Blueprint $table) {
            $table->increments('id_element_type');
            $table->string('element_type', 100);
        });

        DB::table('sys_element_types')->insert([	
            ['id_element_type' => '1','element_type' => 'Área de competencia'],
            ['id_element_type' => '2','element_type' => 'Módulo'],
            ['id_element_type' => '3','element_type' => 'Curso'],
            ['id_element_type' => '4','element_type' => 'Tema'],
            ['id_element_type' => '5','element_type' => 'Subtema'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_element_types');
    }
}
