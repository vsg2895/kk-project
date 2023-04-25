<?php

use Illuminate\Database\Seeder;

class SchoolAddonsSeeder extends Seeder
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
            DB::table('schools_addons')->insert([
                'school_id' => $school->id,
                'addon_id' => $faker->numberBetween(1, 3),
                'price' => $faker->numberBetween(200, 500),
            ]);
        }
    }
}
