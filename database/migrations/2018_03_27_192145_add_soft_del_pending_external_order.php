<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddSoftDelPendingExternalOrder
 */
class AddSoftDelPendingExternalOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_external_orders', function (Blueprint $table)
        {
            $table->softDeletes()->after('data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_external_orders', function (Blueprint $table)
        {
            $table->dropSoftDeletes();
        });
    }
}
