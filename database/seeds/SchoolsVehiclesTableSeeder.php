<?php

use Illuminate\Database\Seeder;

class SchoolsVehiclesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehicles = DB::table('vehicles')->get();
        $schools = DB::table('schools')->get();
        $faker = \Faker\Factory::create();

        foreach ($schools as $school) {
            foreach ($vehicles as $vehicle) {
                DB::table('schools_vehicles')->insert([
                    'school_id' => $school->id,
                    'vehicle_id' => $vehicle->id,
                ]);
            }
        }
    }
}
