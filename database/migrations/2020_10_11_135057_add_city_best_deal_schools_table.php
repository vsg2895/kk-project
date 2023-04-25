<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCityBestDealSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign('cities_school_id_foreign');
            $table->dropColumn('school_id');
        });

        Schema::create('city_best_deal_schools', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
            $table->integer('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
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
        Schema::table('city_best_deal_schools', function (Blueprint $table) {
            $table->dropForeign('city_best_deal_schools_school_id_foreign');
            $table->dropForeign('city_best_deal_schools_city_id_foreign');
        });
        Schema::dropIfExists('city_best_deal_schools');
    }
}
