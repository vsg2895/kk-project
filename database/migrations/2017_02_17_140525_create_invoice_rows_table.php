<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_rows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->integer('amount');
            $table->integer('quantity');
            $table->integer('invoice_id')->unsigned()->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
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
        Schema::table('invoice_rows', function (Blueprint $table) {
            $table->dropForeign('invoice_rows_invoice_id_foreign');
        });
        Schema::dropIfExists('invoice_rows');
    }
}
