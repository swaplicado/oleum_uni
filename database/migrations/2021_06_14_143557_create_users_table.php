<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 100)->unique();
            $table->string('password', 100);
            $table->string('email', 100);
            $table->string('num_employee', 15);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('names', 100);
            $table->string('full_name', 150);
            $table->boolean('is_deleted');
            $table->integer('job_id')->unsigned();
            $table->integer('user_type_id')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('user_type_id')->references('id_user_type')->on('adm_user_types')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
