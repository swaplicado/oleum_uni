<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysTakenStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_take_status', function (Blueprint $table) {	
            $table->increments('id_status');
            $table->string('status', 100);
            $table->string('code', 5)->unique();
        });	
            
        DB::table('sys_take_status')->insert([	
            ['id_status' => '5','status' => 'Cursando','code' => 'CUR'],
            ['id_status' => '6','status' => 'Evaluando','code' => 'EVA'],
            ['id_status' => '7','status' => 'Completado','code' => 'COM'],
        ]);	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_take_status');
    }
}
