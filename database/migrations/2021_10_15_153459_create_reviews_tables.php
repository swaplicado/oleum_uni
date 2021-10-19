<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_review_types', function (Blueprint $table) {
            $table->increments('id_review_type');
            $table->string('code_type', 10);
            $table->string('review_type', 50);
            $table->boolean('is_deleted');
            $table->timestamps();
        });
            
        DB::table('uni_review_types')->insert([
            ['id_review_type' => '1','code_type' => 'PLA','review_type' => 'Plataforma','is_deleted' => '0'],
            ['id_review_type' => '2','code_type' => 'CUR','review_type' => 'Curso','is_deleted' => '0'],
            ['id_review_type' => '3','code_type' => 'EVA','review_type' => 'Evaluacion','is_deleted' => '0'],
            ['id_review_type' => '4','code_type' => 'CAP','review_type' => 'Capacitación','is_deleted' => '0'],
            ['id_review_type' => '5','code_type' => 'CON','review_type' => 'Contenido','is_deleted' => '0'],
        ]);

        Schema::create('uni_reviews', function (Blueprint $table) {
            $table->increments('id_review');
            $table->integer('stars');
            $table->string('review_n_comments', 500)->nullable();
            $table->boolean('is_deleted');
            $table->integer('review_type_id')->unsigned();
            $table->integer('reference_id')->unsigned();
            $table->integer('showed_type_id')->unsigned();
            $table->integer('showed_reference_id')->unsigned();
            $table->integer('student_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('review_type_id')->references('id_review_type')->on('uni_review_types')->onDelete('cascade');
            $table->foreign('showed_type_id')->references('id_review_type')->on('uni_review_types')->onDelete('cascade');
            $table->foreign('student_by_id')->references('id')->on('users')->onDelete('cascade');
        });	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uni_reviews');
        Schema::dropIfExists('uni_review_types');
    }
}
