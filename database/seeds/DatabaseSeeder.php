<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $this->call(OrganizationsTableSeeder::class);
            $this->call(SchoolsTableSeeder::class);
            $this->call(UsersTableSeeder::class);
            $this->call(CoursesTableSeeder::class);
            $this->call(OrdersTableSeeder::class);
            $this->call(OrderItemsTableSeeder::class);
            $this->call(CourseParticipantsTableSeeder::class);
            $this->call(ContactRequestTableSeeder::class);
            $this->call(SchoolSegmentPricesTableSeeder::class);
            $this->call(SchoolsVehicleSegmentsTableSeeder::class);
            $this->call(SchoolsVehiclesTableSeeder::class);
            $this->call(SchoolRatingsTableSeeder::class);
            $this->call(SchoolAddonsSeeder::class);
            $this->call(PostsTableSeeder::class);
            $this->call(CommentsTableSeeder::class);
        } catch (Exception $e) {
            dd($e->getFile(), $e->getLine(), $e->getMessage());
        }
    }
}
