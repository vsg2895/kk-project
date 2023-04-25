<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id')->unsigned()->nullable();
            $table->foreign('school_id')->references('id')->on('schools');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->boolean('invoice_sent')->default(false);
            $table->integer('invoice_id')->unsigned()->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->string('payment_method');
            $table->timestamp('paid_at')->nullable();
            $table->text('comment')->nullable();
            $table->string('external_order_id')->index()->nullable();
            $table->string('external_reservation_id')->index()->nullable();
            $table->boolean('cancelled')->default(false);
            $table->boolean('handled')->default(false);
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_user_id_foreign');
            $table->dropForeign('orders_invoice_id_foreign');
            $table->dropForeign('orders_school_id_foreign');
        });
        Schema::dropIfExists('orders');
    }
}
