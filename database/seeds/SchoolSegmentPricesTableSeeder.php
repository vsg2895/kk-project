<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Jakten\Helpers\Prices;

class SchoolSegmentPricesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schools = DB::table('schools')->get();
        $faker = \Faker\Factory::create('sv_SE');

        foreach ($schools as $school) {
            $prices = [];
            $segments = DB::table('vehicle_segments')->get();

            foreach ($segments as $segment) {
                $subjectToChange = $faker->boolean(10);
                $price = $segment->default_price;

                if (!$price) {
                    if ($segment->name == Prices::DRIVING_TEST_WARM_UP_CAR) {
                        $price = -500;
                    } else {
                        $price = $faker->numberBetween(100, 1000);
                    }
                }

                $prices[] = [
                    'school_id' => $school->id,
                    'vehicle_segment_id' => $segment->id,
                    'amount' => $price,
                    'quantity' => strpos($segment->name, 'DRIVING_LESSON') !== false ? $faker->numberBetween(30, 90) : 1,
                    'comment' => $segment->default_comment ?: $subjectToChange ? $faker->sentence(3) : null,
                    'subject_to_change' => $subjectToChange,
                ];
            }

            DB::table('school_segment_prices')->insert($prices);
        }
    }
}
