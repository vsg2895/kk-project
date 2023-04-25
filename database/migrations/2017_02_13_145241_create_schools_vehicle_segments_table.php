<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsVehicleSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools_vehicle_segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
            $table->integer('vehicle_segment_id')->unsigned();
            $table->foreign('vehicle_segment_id')->references('id')->on('vehicle_segments');
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
        Schema::table('schools_vehicle_segments', function (Blueprint $table) {
            $table->dropForeign('schools_vehicle_segments_school_id_foreign');
            $table->dropForeign('schools_vehicle_segments_vehicle_segment_id_foreign');
        });
        Schema::dropIfExists('schools_vehicle_segments');
    }
}
