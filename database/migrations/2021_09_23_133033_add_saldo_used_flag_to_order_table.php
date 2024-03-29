<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSaldoUsedFlagToOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('saldo_used')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('saldo_used');
        });
    }
}
