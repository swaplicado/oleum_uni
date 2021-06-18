<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adm_companies', function (Blueprint $table) {
            $table->increments('id_company');
            $table->string('company', 100);
            $table->string('acronym', 100);
            $table->boolean('is_deleted');
            $table->integer('external_id')->unsigned();
            $table->integer('head_user_id')->unsigned();
            $table->timestamps();

            $table->foreign('head_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_companies');
    }
}
