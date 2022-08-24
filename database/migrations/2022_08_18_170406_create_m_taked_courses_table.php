<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMTakedCoursesTable extends Migration
{
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)->create('m_taked_courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('assignment_id');
            $table->bigInteger('student_id');
            $table->bigInteger('course_id');
            $table->bigInteger('module_id');
            $table->text('element_body')->nullable();
            $table->int('grade')->nullable();
            $table->boolean('is_delete');
            $table->datetime('dt_open');
            $table->datetime('dt_end');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('m_taked_courses');
    }
}
