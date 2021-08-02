<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointsControl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_points_control', function (Blueprint $table) {
            $table->bigIncrements('id_points_control');
            $table->date('dt_date');
            $table->decimal('increment', 8,2);
            $table->decimal('decrement', 8,2);
            $table->string('comments', 200);
            $table->enum('mov_class', ['mov_in', 'mov_out']);
            $table->integer('mov_type_id')->unsigned();
            $table->bigInteger('assignment_n_id')->unsigned()->nullable();
            $table->integer('student_id')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('mov_type_id')->references('id_mov_type')->on('sys_points_mov_types')->onDelete('cascade');
            $table->foreign('assignment_n_id')->references('id_assignment')->on('uni_assignments')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('uni_points_control');
    }
}
