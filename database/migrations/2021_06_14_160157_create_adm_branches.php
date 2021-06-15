<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmBranches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adm_branches', function (Blueprint $table) {
            $table->increments('id_branch');
            $table->string('branch', 100);
            $table->string('acronym', 100);
            $table->boolean('is_deleted');
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
        Schema::dropIfExists('adm_branches');
    }
}
