<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysMovStockTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_stk_mov_types', function (Blueprint $table) {
            $table->increments('id_mov_type');
            $table->string('code', 5)->unique();
            $table->string('movement_type', 150);
            $table->enum('mov_class', ['mov_in', 'mov_out']);
        });

        DB::table('sys_stk_mov_types')->insert([	
            ['id_mov_type' => '1','code' => 'COMP','movement_type' => 'Compra (alta de producto)','mov_class' => 'mov_in'],
            ['id_mov_type' => '2','code' => 'BAJA','movement_type' => 'Baja por daño o extravío','mov_class' => 'mov_out'],
            ['id_mov_type' => '3','code' => 'CANJ','movement_type' => 'Canje por puntos','mov_class' => 'mov_out'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_stk_mov_types');
    }
}
