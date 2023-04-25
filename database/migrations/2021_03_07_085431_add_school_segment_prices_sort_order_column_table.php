<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchoolSegmentPricesSortOrderColumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_segment_prices', function (Blueprint $table) {
            $table->integer('sort_order')->nullable(true)->default(NULL);
        });
        Schema::table('schools_addons', function (Blueprint $table) {
            $table->integer('sort_order')->nullable(true)->default(NULL);
        });
        Schema::table('custom_addons', function (Blueprint $table) {
            $table->integer('sort_order')->nullable(true)->default(NULL);
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
            $table->dropColumn('sort_order');
        });
        Schema::table('schools_addons', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
        Schema::table('custom_addons', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
}
