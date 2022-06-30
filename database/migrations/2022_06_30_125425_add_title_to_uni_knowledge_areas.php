<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleToUniKnowledgeAreas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uni_knowledge_areas', function (Blueprint $table) {
            $table->string('knowledge_area_title')->nullable()->after('id_knowledge_area');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uni_knowledge_areas', function (Blueprint $table) {
            $table->dropColumn('knowledge_area_title');
        });
    }
}
