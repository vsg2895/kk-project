<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vehicle_segment_id')->unsigned();
            $table->foreign('vehicle_segment_id')->references('id')->on('vehicle_segments');
            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
            $table->integer('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->dateTime('start_time')->index();
            $table->integer('length_minutes');
            $table->decimal('price');
            $table->string('address');
            $table->text('address_description')->nullable();
            $table->text('description')->nullable();
            $table->text('confirmation_text');
            $table->integer('seats');
            $table->integer('seats_available');
            $table->softDeletes();
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
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign('courses_school_id_foreign');
            $table->dropForeign('courses_city_id_foreign');
            $table->dropForeign('courses_vehicle_segment_id_foreign');
        });

        Schema::dropIfExists('courses');
    }
}
