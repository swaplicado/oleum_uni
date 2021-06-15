<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysElementStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_element_status', function (Blueprint $table) {
            $table->increments('id_element_status');
            $table->string('element_status', 100);
            $table->string('code', 5)->unique();
        });

        DB::table('sys_element_status')->insert([	
            ['id_element_status' => '1','element_status' => 'Nuevo','code' => 'NVO'],
            ['id_element_status' => '2','element_status' => 'Editando','code' => 'EDT'],
            ['id_element_status' => '3','element_status' => 'Publicado','code' => 'PUB'],
        ]);	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_element_status');
    }
}
