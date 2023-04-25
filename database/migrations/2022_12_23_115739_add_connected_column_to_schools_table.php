<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConnectedColumnToSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->boolean('connected_to')->default(0)->after('top_partner');
            $table->timestamp('connected_at')->nullable()->default(null)->after('connected_to');
            $table->integer('rule_id')->nullable()->default(null)->after('city_id');
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
            $table->dropColumn([
                'connected_to', 'connected_at',
            ]);
        });
    }
}
