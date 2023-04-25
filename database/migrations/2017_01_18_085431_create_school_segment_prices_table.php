<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolSegmentPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_segment_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
            $table->integer('vehicle_segment_id')->unsigned();
            $table->foreign('vehicle_segment_id')->references('id')->on('vehicle_segments');
            $table->decimal('amount')->nullable();
            $table->integer('quantity')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('subject_to_change')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_segment_prices', function (Blueprint $table) {
            $table->dropForeign('school_segment_prices_school_id_foreign');
            $table->dropForeign('school_segment_prices_vehicle_segment_id_foreign');
        });

        Schema::dropIfExists('school_segment_prices');
    }
}
