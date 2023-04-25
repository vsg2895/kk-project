<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_segments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->integer('vehicle_id')->unsigned();
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->boolean('editable')->default(true);
            $table->boolean('bookable')->default(false);
            $table->boolean('comparable')->default(true);
            $table->integer('default_price')->nullable();
            $table->text('default_comment')->nullable();
            $table->text('description')->nullable();
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        $this->insert();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropForeign('prices_vehicle_id_foreign');
        });

        Schema::dropIfExists('prices');
    }

    public function insert()
    {
        $segments = [
            [
                'name' => 'MANDATORY_EXPENSES_CAR',
                'vehicle_id' => 1,
                'default_price' => 1470,
                'comparable' => true,
                'bookable' => false,
                'default_comment' => 'Betalas inte till trafikskolan.',
                'editable' => false,
                'description' => 'Körkortstillstånd, Kunskapsprov, Uppkörning, Tillverkning av körkort, syntest m.m',
                'explanation' => null,
            ],
            [
                'name' => 'MANDATORY_EXPENSES_MOPED',
                'vehicle_id'=> 3,
                'default_price' => 625,
                'default_comment' => 'Betalas inte till trafikskolan.',
                'editable' => false,
                'comparable' => true,
                'bookable' => false,
                'description' => 'Körkortstillstånd, teoriprov på trafikverket och körkortstillverkning.',
                'explanation' => null,
            ],
            [
                'name' => 'DRIVING_LESSON_CAR',
                'vehicle_id' => 1,
                'default_price' => null,
                'default_comment' => '600 minuter körlektion',
                'editable' => true,
                'comparable' => true,
                'bookable' => false,
                'description' => 'Varje person behöver i genomsnitt 15 st körlektioner x 40min',
                'explanation' => null,
            ],

            [
                'name' => 'ENROLLMENT_CAR',
                'vehicle_id' => 1,
                'default_price' => null,
                'default_comment' => null,
                'editable' => true,
                'comparable' => true,
                'bookable' => false,
                'description' => null,
                'explanation' => null,
            ],
            [
                'name' => 'LITERATURE_CAR',
                'vehicle_id' => 1,
                'default_price' => 350,
                'default_comment' => 'Pris för Körkortsboken.',
                'editable' => false,
                'comparable' => true,
                'bookable' => false,
                'description' => null,
                'explanation' => null,
            ],
            [
                'name' => 'RISK_ONE_CAR',
                'vehicle_id' => 1,
                'default_price' => null,
                'default_comment' => null,
                'editable' => true,
                'comparable' => true,
                'bookable' => true,
                'description' => null,
                'explanation' => null,
            ],
            [
                'name' => 'INTRODUCTION_CAR',
                'vehicle_id' => 1,
                'default_price' => 450,
                'default_comment' => null,
                'editable' => true,
                'comparable' => true,
                'bookable' => true,
                'description' => null,
                'explanation' => 'Om ni inte erbjuder Introduktionskurs kommer Körkortsjakten lägga till ett schablon pris på 450kr',
            ],
            [
                'name' => 'MANDATORY_EXPENSES_MC',
                'vehicle_id' => 2,
                'default_price' => 2195,
                'default_comment' => 'Betalas inte till trafikskolan.',
                'editable' => false,
                'comparable' => true,
                'bookable' => false,
                'description' => 'Körkortstillstånd, Kunskapsprov, Uppkörning, Tillverkning av Körkort, syntest m.m.',
                'explanation' => null,
            ],
            [
                'name' => 'DRIVING_LESSON_MC',
                'vehicle_id' => 2,
                'default_price' => null,
                'default_comment' => '400 min körlektion',
                'editable' => true,
                'comparable' => true,
                'bookable' => false,
                'description' => 'Varje person behöver i genomsnitt 10 lektioner á 40 min.',
                'explanation' => null,
            ],
            [
                'name' => 'RISK_ONE_MC',
                'vehicle_id' => 2,
                'default_price' => null,
                'default_comment' => null,
                'editable' => true,
                'comparable' => true,
                'bookable' => true,
                'description' => null,
                'explanation' => null,
            ],
            [
                'name' => 'RISK_TWO_MC',
                'vehicle_id' => 2,
                'default_price' => null,
                'default_comment' => null,
                'editable' => true,
                'comparable' => true,
                'bookable' => false,
                'description' => null,
                'explanation' => null,
            ],
            [
                'name' => 'BORROW_MC_DRIVING_TEST',
                'vehicle_id' => 2,
                'default_price' => null,
                'default_comment' => null,
                'editable' => true,
                'comparable' => true,
                'bookable' => false,
                'description' => null,
                'explanation' => null,
            ],
            [
                'name' => 'RISK_TWO_CAR',
                'vehicle_id' => 1,
                'default_price' => 1800,
                'default_comment' => null,
                'editable' => true,
                'comparable' => true, 
                'bookable' => false,
                'description' => null,
                'explanation' => 'Om ni inte erbjuder Risktvåan kommer Körkortsjakten lägga till ett schablon pris på 1800 kr',
            ],

            [
                'name' => 'BORROW_CAR_DRIVING_TEST',
                'vehicle_id' => 1,
                'default_price' => null,
                'default_comment' => null,
                'editable' => true,
                'comparable' => true,
                'bookable' => false,
                'description' => null,
                'explanation' => null,
            ],

            [
                'name' => 'MOPED_PACKAGE',
                'vehicle_id' => 3,
                'default_price' => null,
                'default_comment' => null,
                'editable' => true,
                'comparable' => true,
                'bookable' => true,
                'description' => null,
                'explanation' => null,
            ],
            [
                'name' => 'THEORY_LESSON_CAR',
                'vehicle_id' => 1,
                'default_price' => null,
                'default_comment' => null,
                'editable' => true,
                'comparable' => false,
                'bookable' => true,
                'description' => null,
                'explanation' => null,
            ],
        ];

        \DB::table('vehicle_segments')->insert($segments);
    }
}
