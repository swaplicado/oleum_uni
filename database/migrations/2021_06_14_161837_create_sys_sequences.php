<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysSequences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_sequences', function (Blueprint $table) {
            $table->increments('id_sequence');
            $table->string('sequence', 100);
            $table->string('code', 5)->unique();
        });

        DB::table('sys_sequences')->insert([	
            ['id_sequence' => '1','sequence' => 'Aleatoria','code' => 'ALE'],
            ['id_sequence' => '2','sequence' => 'Secuencial','code' => 'SEC'],
        ]);	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_sequences');
    }
}
