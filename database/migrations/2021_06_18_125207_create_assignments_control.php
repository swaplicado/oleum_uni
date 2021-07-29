<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignmentsControl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_assignments_control', function (Blueprint $table) {	
            $table->bigIncrements('id_control');
            $table->boolean('is_deleted');
            $table->date('dt_assignment');
            $table->date('dt_end');
            $table->integer('knowledge_area_id')->unsigned();
            $table->integer('organization_n_id')->unsigned()->nullable();
            $table->integer('company_n_id')->unsigned()->nullable();
            $table->integer('branch_n_id')->unsigned()->nullable();
            $table->integer('department_n_id')->unsigned()->nullable();
            $table->integer('job_n_id')->unsigned()->nullable();
            $table->integer('student_n_id')->unsigned()->nullable();
            $table->integer('scheduled_n_id')->unsigned()->nullable();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('knowledge_area_id')->references('id_knowledge_area')->on('uni_knowledge_areas')->onDelete('cascade');
            $table->foreign('organization_n_id')->references('id_organization')->on('adm_organizations')->onDelete('cascade');
            $table->foreign('company_n_id')->references('id_company')->on('adm_companies')->onDelete('cascade');
            $table->foreign('branch_n_id')->references('id_branch')->on('adm_branches')->onDelete('cascade');
            $table->foreign('department_n_id')->references('id_department')->on('adm_departments')->onDelete('cascade');
            $table->foreign('job_n_id')->references('id_job')->on('adm_jobs')->onDelete('cascade');
            $table->foreign('student_n_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('scheduled_n_id')->references('id_scheduled')->on('uni_scheduled_assignments')->onDelete('cascade');
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
        Schema::dropIfExists('uni_assignments_control');
    }
}
