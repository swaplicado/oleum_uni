    <?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewCfgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uni_review_cfgs', function (Blueprint $table) {
            $table->increments('id_configuration');
            $table->string('question', 250);
            $table->enum('review_form', ['stars', 'text']);
            $table->boolean('is_deleted');
            $table->integer('review_type_id')->unsigned();
            $table->integer('reference_id')->unsigned();
            $table->integer('showed_type_id')->unsigned();
            $table->integer('showed_reference_id')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('review_type_id')->references('id_review_type')->on('uni_review_types')->onDelete('cascade');
            $table->foreign('showed_type_id')->references('id_review_type')->on('uni_review_types')->onDelete('cascade');
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
        Schema::dropIfExists('uni_review_cfgs');
    }
}
