<?php

use Illuminate\Database\Migrations\Migration;

class CreateSchoolCalculatedPricesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE VIEW school_calculated_prices AS
            SELECT
                school_segment_prices.school_id,
                FLOOR(SUM(IF(vehicle_segments.comparable = 1, IF(vehicle_segments.name like \'DRIVING_LESSON%\', (school_segment_prices.amount/school_segment_prices.quantity)*IF(vehicle_segments.name = \'DRIVING_LESSON_CAR\', 600, 400), school_segment_prices.amount), 0))) as comparison_price,
                SUM(IF(school_segment_prices.amount is not null and vehicle_segments.comparable = 1, 1, 0)) as prices_set,
                vehicle_segments.vehicle_id,
                FLOOR(SUM(IF(vehicle_segments.name like \'DRIVING_LESSON%\', (school_segment_prices.amount/school_segment_prices.quantity)*IF(vehicle_segments.name = \'DRIVING_LESSON_CAR\', 600, 400), 0))) as DRIVING_LESSON,
                FLOOR(SUM(IF(vehicle_segments.name like \'RISK_ONE%\', school_segment_prices.amount, 0))) as RISK_ONE,
                FLOOR(SUM(IF(vehicle_segments.name like \'RISK_TWO%\', school_segment_prices.amount, 0))) as RISK_TWO,
                FLOOR(SUM(IF(vehicle_segments.name like \'INTRODUCTION%\', school_segment_prices.amount, 0))) as INTRODUCTION
                FROM school_segment_prices
            join vehicle_segments on school_segment_prices.vehicle_segment_id = vehicle_segments.id
            group by school_segment_prices.school_id, vehicle_segments.vehicle_id'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW school_calculated_prices');
    }
}
