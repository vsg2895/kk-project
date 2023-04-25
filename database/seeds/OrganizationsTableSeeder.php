<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('sv_SE');

        $organizations = [];
        for ($i = 0; $i < 10; $i++) {
            $organizations[] = [
                'name' => $faker->company() . ' (org)',
                'org_number' => $i == 0 ? 5555555555 : $i,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'sign_up_status' => \Jakten\Helpers\KlarnaSignup::STATUS_NOT_INITIATED,
                'payment_id' => $i == 0 ? 8907 : null,
                'payment_secret' => $i == 0 ? 'aSrcXbXbWoxKKhY' : null,
            ];
        }

        DB::table('organizations')->insert($organizations);
    }
}
