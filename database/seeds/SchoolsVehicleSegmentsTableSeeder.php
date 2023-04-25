<?php

use Illuminate\Database\Seeder;

class SchoolsVehicleSegmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $segments = DB::table('vehicle_segments')->where('bookable', true)->get();
        $schools = DB::table('schools')->get();
        $faker = \Faker\Factory::create();

        foreach ($schools  as $school) {
            foreach ($segments as $segment) {
                if ($faker->boolean(70)) {
                    DB::table('schools_vehicle_segments')->insert([
                        'school_id' => $school->id,
                        'vehicle_segment_id' => $segment->id,
                    ]);
                }
            }
        }
    }
}
