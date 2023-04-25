<?php

use Illuminate\Database\Seeder;

class SchoolRatingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schools = DB::table('schools')->get();
        $faker = \Faker\Factory::create('sv-SE');

        foreach ($schools as $school) {
            $ratingsCount = $faker->numberBetween(1, 20);
            $totalRating = 0;
            for ($i = 0; $i < $ratingsCount; $i++) {
                $rating = $faker->numberBetween(1, 5);
                DB::table('school_ratings')->insert([
                    'school_id' => $school->id,
                    'user_id' => $faker->numberBetween(1, 10),
                    'rating' => $rating,
                ]);
                $totalRating += $rating;
            }

            DB::table('schools')
                ->where('id', $school->id)
                ->update(['average_rating' => $totalRating / $ratingsCount, 'rating_count' => $ratingsCount]);
        }
    }
}
