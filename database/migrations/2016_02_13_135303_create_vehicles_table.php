<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('label');
            $table->timestamps();
        });

        $vehicles = [
            [
                'id' => 1,
                'name' => 'CAR',
                'label' => 'vehicles.car',
            ],
            [
                'id' => 2,
                'name' => 'MC',
                'label' => 'vehicles.mc',
            ],
            [
                'id' => 3,
                'name' => 'MOPED',
                'label' => 'vehicles.moped',
            ],
        ];

        DB::table('vehicles')->insert($vehicles);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
