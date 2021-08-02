<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysMovPointsTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_points_mov_types', function (Blueprint $table) {
            $table->increments('id_mov_type');
            $table->string('code', 5)->unique();
            $table->string('movement_type', 150);
            $table->enum('mov_class', ['mov_in', 'mov_out']);
        });

        DB::table('sys_points_mov_types')->insert([	
            ['id_mov_type' => '1','code' => 'COUR','movement_type' => 'Ganados por curso','mov_class' => 'mov_in'],
            ['id_mov_type' => '2','code' => 'PENA','movement_type' => 'Penalización','mov_class' => 'mov_out'],
            ['id_mov_type' => '3','code' => 'BONI','movement_type' => 'Bonificación','mov_class' => 'mov_in'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_points_mov_types');
    }
}
