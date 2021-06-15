<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmDepartments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adm_departments', function (Blueprint $table) {
            $table->increments('id_department');
            $table->string('department', 100);
            $table->string('acronym', 100);
            $table->boolean('is_deleted');
            $table->integer('head_user_n_id')->unsigned()->nullable();
            $table->integer('department_n_id')->unsigned()->nullable();
            $table->timestamps();

            //$table->foreign('head_user_n_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('department_n_id')->references('id_department')->on('adm_departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_departments');
    }
}
