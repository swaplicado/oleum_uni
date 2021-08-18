<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdContentCarousel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uni_carousel', function (Blueprint $table) {
            $table->bigInteger('content_n_id')->unsigned()->nullable()->after('is_deleted');

            $table->foreign('content_n_id')->references('id_content')->on('uni_edu_contents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uni_carousel', function (Blueprint $table) {
            $table->dropForeign(['content_n_id']);

            $table->dropColumn('content_n_id');
        });
    }
}
