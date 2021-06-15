<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adm_jobs', function (Blueprint $table) {
            $table->increments('id_job');
            $table->string('job', 100);
            $table->string('acronym', 100);
            $table->boolean('is_deleted');
            $table->integer('department_id')->unsigned();
            $table->timestamps();

            $table->foreign('department_id')->references('id_department')->on('adm_departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adm_jobs');
    }
}
