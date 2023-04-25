<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('sv_SE');

        $cities = DB::table('cities')->get();
        $organizations = DB::table('organizations')->get();

        foreach ($cities as $index => $city) {
            foreach ($organizations as $organization) {
                if ($faker->boolean(10)) {
                    $school = DB::table('schools')->insert([
                        'organization_id' => $organization->id,
                        'city_id' => $city->id,
                        'name' => str_replace('(org)','(skola)',$organization->name),
                        'latitude' => $city->latitude + $faker->randomFloat(5, 0, 0.5),
                        'longitude' => $city->longitude + $faker->randomFloat(5, 0, 0.5),
                        'address' => $faker->streetAddress,
                        'zip' => str_replace(' ', '', $faker->postcode),
                        'postal_city' => $city->name,
                        'phone_number' => $faker->e164PhoneNumber,
                        'contact_email' => $faker->companyEmail,
                        'booking_email' => $faker->companyEmail,
                        'website' => 'http://www.' . $faker->domainName,
                        'description' => $faker->realText(),
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now(),
                    ]);
                }
            }
        }
    }
}
