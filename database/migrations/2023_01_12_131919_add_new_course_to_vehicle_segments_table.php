<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewCourseToVehicleSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->insert();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function insert()
    {
        $segment = [
            'name' => 'RISK_ONE_TWO_COMBO_ENGLISH',
            'vehicle_id' => 1,
            'default_price' => null,
            'default_comment' => null,
            'editable' => true,
            'comparable' => true,
            'bookable' => true,
            'description' => 'Package price for Risk Training 1 and Risk Training 2 (Slide Track)',
            'explanation' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'color' => '#4169e1',
            'order' => '3',
            'slug' => 'risk1+2-english',
            'title' => 'Risk 1&2 combo English',
        ];

        DB::table('vehicle_segments')->insert([$segment]);

        $defaultFee = [
            'id' => 40,
            'fee' => 12,
            'created_at' => '2023-01-12 14:44:07',
            'updated_at' => '2023-01-12 14:44:07',
        ];

        DB::table('vehicle_fee')->insert([$defaultFee]);
    }
}
