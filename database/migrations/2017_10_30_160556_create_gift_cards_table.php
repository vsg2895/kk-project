<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('gift_card_type_id');
            $table->integer('buyer_id')->nullable()->unsigned();
            $table->foreign('buyer_id')->references('id')->on('users');
            $table->integer('claimer_id')->nullable()->unsigned();
            $table->foreign('claimer_id')->references('id')->on('users');
            $table->string('token');
            $table->decimal('remaining_balance');
            $table->boolean('claimed')->default(false);
            $table->timestamps();
            $table->timestamp('expires');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gift_cards');
    }
}
