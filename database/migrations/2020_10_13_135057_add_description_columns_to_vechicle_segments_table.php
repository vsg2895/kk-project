<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionColumnsToVechicleSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_segments', function (Blueprint $table) {
            $table->string('slug')->nullable(true)->default(NULL);
            $table->string('title')->nullable(true)->default(NULL);
            $table->string('sub_explanation')->nullable(true)->default(NULL);
            $table->string('sub_description')->nullable(true)->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_segments', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('title');
            $table->dropColumn('sub_explanation');
            $table->dropColumn('sub_description');
        });
    }
}
