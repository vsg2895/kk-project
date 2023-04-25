<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned()->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->integer('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('name');
            $table->double('latitude')->index()->nullable();
            $table->double('longitude')->index()->nullable();
            $table->string('address')->nullable();
            $table->integer('zip')->nullable();
            $table->string('postal_city')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('booking_email')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->float('average_rating')->nullable();
            $table->integer('rating_count')->default(0);
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
            $table->dropForeign('schools_representative_user_id_foreign');
            $table->dropForeign('schools_city_id_foreign');
            $table->dropForeign('schools_organization_id_foreign');
        });

        Schema::dropIfExists('schools');
    }
}
