<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFeeFieldToPricesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schools_addons', function (Blueprint $table) {
            $table->float('fee')->nullable();
        });
        Schema::table('custom_addons', function (Blueprint $table) {
            $table->float('fee')->nullable();
        });
        Schema::table('school_segment_prices', function (Blueprint $table) {
            $table->float('fee')->nullable();
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
            $table->dropColumn('fee');
        });
        Schema::table('custom_addons', function (Blueprint $table) {
            $table->dropColumn('fee');
        });
        Schema::table('schools_addons', function (Blueprint $table) {
            $table->dropColumn('fee');
        });
    }
}
