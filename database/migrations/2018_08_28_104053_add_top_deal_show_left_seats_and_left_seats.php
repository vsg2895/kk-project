<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTopDealShowLeftSeatsAndLeftSeats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_addons', function (Blueprint $table) {
            $table->boolean('top_deal')->default(false);
            $table->boolean('show_left_seats')->default(false);
            $table->smallInteger('left_seats')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_addons', function (Blueprint $table) {
            $table->dropColumn('top_deal');
            $table->dropColumn('show_left_seats');
            $table->dropColumn('left_seats');
        });
    }
}
