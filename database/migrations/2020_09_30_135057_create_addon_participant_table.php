<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class createAddonParticipantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('addon_participants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_item_id')->unsigned();
            $table->foreign('order_item_id')->references('id')->on('order_items');
            $table->integer('addon_id')->unsigned();
            $table->string('given_name');
            $table->string('family_name');
            $table->string('email')->nullable();
            $table->string('social_security_number');
            $table->string('type');
            $table->string('transmission')->nullable(true)->default(NULL);
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
        Schema::table('addon_participants', function (Blueprint $table) {
            $table->dropForeign('addon_participants_order_item_id_foreign');
        });
        Schema::dropIfExists('addon_participants');
    }
}
