<?php

use Illuminate\Database\Seeder;
use Jakten\Models\Addon;
use Jakten\Models\CustomAddon;
use Jakten\Models\OrderItem;

class OrderItemsTableSeeder extends Seeder
{
    //Percentage to seed an addon
    const ADDON_CHANCE = 30;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->setupAddonTypes();

        $orders = \Jakten\Models\Order::with('school.courses')->get();
        $faker = \Faker\Factory::create('sv_SE');
        foreach ($orders as $key => $order) {
            //Make sure that we have at least 1 OrderItem with a course.
            $hasOneCourse = false;

            $coursesIds = $order->school->courses->map(function ($course) {
                return $course->id;
            })->toArray();

            $nr = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $nr; $i++) {
                $type = null;
                $courseId = null;

                $isAddon = $hasOneCourse && (rand(1, 100) <= static::ADDON_CHANCE);
                if ($isAddon) {
                    $type = $this->getAddonType();
                } else {
                    $courseId = $this->getCourseId($coursesIds);
                    $hasOneCourse = true;
                    $type = "Placeholder type";
                }

                $orderItemData = [
                    'order_id' => $order->id,
                    'school_id' => $order->school->id,
                    'course_id' => $courseId,
                    'amount' => 250 * $faker->numberBetween(2, 5),
                    'quantity' => $faker->numberBetween(1, 2),
                    'type' => $type];

                OrderItem::create($orderItemData);
            }
        }

    }

    private function getAddonType()
    {
        return static::$addonTypes[array_rand(static::$addonTypes)];
    }

    private static $addonTypes = [];
    private function setupAddonTypes(){
        $addons = Addon::all();
        $addonTypes = $addons->map(function($addon) {
            /** @var Addon $addon */
            return $addon->name;
        });

        //TODO: Need to make an custom addon seeder
        $customAddons = CustomAddon::all();
        $customAddonTypes = $customAddons->map(function($customAddon) {
            /** @var CustomAddon $customAddon */
            return $customAddon->name;
        });

        static::$addonTypes = array_merge($addonTypes->toArray(), $customAddonTypes->toArray());
    }

    /**
     * @param $coursesIds
     * @return mixed
     */
    private function getCourseId($coursesIds)
    {
        return $coursesIds[array_rand($coursesIds)];
    }
}
