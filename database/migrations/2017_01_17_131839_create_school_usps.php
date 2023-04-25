<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolUsps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_usps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('text');
            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools');
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
        Schema::table('school_usps', function (Blueprint $table) {
            $table->dropForeign('schools_school_id_foreign');
        });
        Schema::dropIfExists('school_usps');
    }
}
