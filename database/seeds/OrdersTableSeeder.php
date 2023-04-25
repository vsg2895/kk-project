<?php

use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    const CREATE_ORDER_CHANCE = 40;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $courses = DB::table('courses')->get();
        $faker = \Faker\Factory::create('sv_SE');
        foreach ($courses as $key=>$course) {
            //Only create a order for X percent of the courses.
            if(rand(1, 100) <= static::CREATE_ORDER_CHANCE){
                continue;
            }

            $nr = $faker->numberBetween(1, 3);
            $subDays = $faker->numberBetween(0, 1000);
            $date = \Carbon\Carbon::now()->subDays($subDays);
            for ($i = 0; $i < $nr; $i++) {
                DB::table('orders')->insert([
                    'school_id' => $course->school_id,
                    'user_id' => $faker->numberBetween(8, 12),
                    'cancelled' => $faker->numberBetween(1, 4) === 1,
                    'handled' => true,
                    'payment_method' => '',
                    'paid_at' => \Carbon\Carbon::now(),
                    'created_at' => $date,
                    'invoice_sent' => $faker->numberBetween(1, 4) < 4,
                    //'updated_at' => \Carbon\Carbon::now(),
                    ]);
            }
        }
    }
}
