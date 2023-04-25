<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses_order', function (Blueprint $table) {
            $table->date('date');
            $table->integer('school_id');
            $table->integer('user_id');
            $table->integer('city_id');
            $table->integer('vehicle_segment');
            $table->integer('order');
            $table->primary(['date', 'school_id', 'vehicle_segment', 'city_id']);
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
        Schema::dropIfExists('courses_order');
    }
}
