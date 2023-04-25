<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftCardTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_card_types', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('price');
            $table->decimal('value');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('days_valid')->default(60);
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
        Schema::dropIfExists('gift_card_types');
    }
}
