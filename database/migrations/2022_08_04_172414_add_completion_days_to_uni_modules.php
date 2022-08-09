<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompletionDaysToUniModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uni_modules', function (Blueprint $table) {
            $table->integer('completion_days')->after('objectives');
            $table->bigInteger('pre_module_id')->nullable()->after('completion_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uni_modules', function (Blueprint $table) {
            $table->dropColumn('pre_module_id');
            $table->dropColumn('completion_days');
        });
    }
}
