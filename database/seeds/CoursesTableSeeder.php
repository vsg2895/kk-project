<?php

use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Jakten\Helpers\Courses;
use Jakten\Models\Course;
use Jakten\Models\School;
use Jakten\Models\VehicleSegment;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $cityId = null;
        $schools = School::query();

        if (env('APP_ENV') !== 'production') {
            $cityId = $this->command->ask("Enter City ID or leave empty", null);
            $schools = $cityId ? $schools->where('city_id', $cityId)->get() : $schools->get();
        }

        $faker = Factory::create('sv_SE');

        $schools->each(function (School $school) use ($faker, $cityId) {
            $vehicleSegments = VehicleSegment::query()
                ->where('bookable', 1)
                ->get();

            $vehicleSegments->each(function (VehicleSegment $vehicleSegment) use ($faker, $school, $cityId) {
                array_map(function () use ($faker, $vehicleSegment, $school, $cityId) {
                    try {
                        /** @var Course $course */
                        $course = Course::create([
                            'vehicle_segment_id' => $vehicleSegment->id,
                            'school_id' => $school->id,
                            'city_id' => $cityId ?? $school->city_id,
                            'created_at' => Carbon::now(),
                            'start_time' => Carbon::now()
                                ->addDay($faker->numberBetween(30, 60))
                                ->addHours($faker->numberBetween(1, 23))
                                ->format('Y-m-d H:i'),
                            'length_minutes' => $faker->numberBetween(30, 90),
                            'price' => $faker->numberBetween(6, 16) * 50,
                            'address' => $school->address,
                            'address_description' => 'Portkod 1111, 2 trappor.',
                            'description' => 'Vi bjuder på kaffe och smörgås.',
                            'confirmation_text' => 'Tack för din bokning, medtag legitimation!',
                            'seats' => $faker->numberBetween(1, 10)
                        ]);

                        $this->command->info("Course #{$course->id} has been created");
                    } catch (Exception $exception) {
                        $this->command->error("Failed to create a course due to exception : {$exception->getMessage()}");
                        \Illuminate\Support\Facades\Log::error($exception);
                    }
                }, range(1, $faker->numberBetween(1, 3)));
            });
        });
    }
}
