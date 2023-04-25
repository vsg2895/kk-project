<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        DB::table('addons')->insert([
            ['name' => 'Övningskörningsskylt'],
            ['name' => 'Handledarboken'],
            ['name' => 'Körkortsboken'],
            ['name' => 'Testlektion'],
            ['name' => 'Körlektion x5'],
            ['name' => 'Körlektion x10'],
            ['name' => 'Syntest'],
            ['name' => 'MC-reflexväst/varselväst'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addons');
    }
}
