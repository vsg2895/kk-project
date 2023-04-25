<?php

use Illuminate\Database\Seeder;
use Jakten\Models\CourseParticipant;
use Jakten\Models\OrderItem;

class CourseParticipantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('sv_SE');
        $orderItems = OrderItem::orWhereNotNull('course_id')->get(['id', 'course_id']);

        foreach ($orderItems as $orderItem) {
            /** @var OrderItem $orderItem*/
            $participantData = [
                'order_item_id' => $orderItem->id,
                'course_id' => $orderItem->course_id,
                'given_name' => $faker->firstName,
                'family_name' => $faker->lastName,
                'social_security_number' => '5555555555',
                'type' => ''
            ];

            CourseParticipant::create($participantData);
        }
    }
}
