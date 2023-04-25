<?php

use Illuminate\Database\Seeder;

class ContactRequestTableSeeder extends Seeder
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
        foreach ($schools as $key=>$school) {
            $nr = $faker->numberBetween(1, 10);
            for ($i = 0; $i < $nr; $i++) {
                DB::table('contact_requests')->insert([
                    'school_id' => $school->id,
                    'email' => $faker->email(),
                    'title' => $faker->word(),
                    'name' => '',
                    'message' => $faker->sentence($nbWords = 6, $variableNbWords = true),
                ]);
            }
        }
    }
}
