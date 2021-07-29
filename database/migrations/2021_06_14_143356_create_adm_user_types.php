<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmUserTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adm_user_types', function (Blueprint $table) {
            $table->increments('id_user_type');
            $table->string('user_type', 100);
            $table->boolean('is_deleted');
            $table->timestamps();
        });

        DB::table('adm_user_types')->insert([	
            ['id_user_type' => '1','user_type' => 'ESTÁNDAR', 'is_deleted' => '0'],
            ['id_user_type' => '2','user_type' => 'ADMINISTRADOR', 'is_deleted' => '0'],
            ['id_user_type' => '3','user_type' => 'GH', 'is_deleted' => '0'],
            ['id_user_type' => '4','user_type' => 'ADMINISTRADOR SISTEMA', 'is_deleted' => '0'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_user_types');
    }
}
