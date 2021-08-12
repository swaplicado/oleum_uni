<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftsStock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_gifts_stock', function (Blueprint $table) {
            $table->bigIncrements('id_stock');
            $table->date('dt_date');
            $table->decimal('increment', 8,2);
            $table->decimal('decrement', 8,2);
            $table->string('comments', 200);
            $table->enum('mov_class', ['mov_in', 'mov_out']);
            $table->integer('mov_type_id')->unsigned();
            $table->integer('gift_id')->unsigned();
            $table->integer('student_n_id')->unsigned()->nullable();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('mov_type_id')->references('id_mov_type')->on('sys_stk_mov_types')->onDelete('cascade');
            $table->foreign('gift_id')->references('id_gift')->on('uni_gifts')->onDelete('cascade');
            $table->foreign('student_n_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('uni_gifts_stock');
    }
}
