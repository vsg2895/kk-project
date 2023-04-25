<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('desc_trafikskolor')->nullable();
            $table->text('desc_introduktionskurser')->nullable();
            $table->text('desc_riskettan')->nullable();
            $table->text('desc_teorilektion')->nullable();
            $table->text('desc_risktvaan')->nullable();
            $table->text('desc_riskettanmc')->nullable();
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
        Schema::table('city_infos', function (Blueprint $table) {
            $table->dropForeign('city_infos_city_id_foreign');
        });

        Schema::dropIfExists('city_infos');
    }
}
