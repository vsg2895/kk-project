<?php

use \Jakten\Helpers\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('sv_SE');
        $this->createStudents($faker);
        $this->createAdmins($faker);
        $this->createOrganizationUsers($faker);
    }

    protected function createStudents(\Faker\Generator $faker)
    {
        $users = [];
        for ($i = 1; $i < 6; $i++) {
            $users[] = [
                'email' => "student{$i}@jakten.dev",
                'password' => bcrypt('jakten'),
                'role_id' => Roles::ROLE_STUDENT,
                'given_name' => $faker->firstName,
                'family_name' => $faker->lastName,
                'phone_number' => $faker->e164PhoneNumber,
                'created_at' => \Carbon\Carbon::now(),
                'confirmed' => true,
            ];
        }

        DB::table('users')->insert($users);
    }

    protected function createOrganizationUsers(\Faker\Generator $faker)
    {
        $users = [];
        $count = DB::table('organizations')->count();
        for ($i = 1; $i < $count + 1; $i++) {
            $users[] = [
                'email' => "employee{$i}@jakten.dev",
                'password' => bcrypt('jakten'),
                'role_id' => Roles::ROLE_ORGANIZATION_USER,
                'given_name' => $faker->firstName,
                'family_name' => $faker->lastName,
                'phone_number' => $faker->e164PhoneNumber,
                'created_at' => \Carbon\Carbon::now(),
                'organization_id' => $i,
                'confirmed' => true,
            ];
        }

        DB::table('users')->insert($users);
    }

    protected function createAdmins(\Faker\Generator $faker)
    {
        $users = [];
        for ($i = 1; $i < 6; $i++) {
            $users[] = [
                'email' => "admin{$i}@jakten.dev",
                'password' => bcrypt('jakten'),
                'role_id' => Roles::ROLE_ADMIN,
                'given_name' => $faker->firstName,
                'family_name' => $faker->lastName,
                'phone_number' => $faker->e164PhoneNumber,
                'created_at' => \Carbon\Carbon::now(),
                'confirmed' => true,
            ];
        }

        DB::table('users')->insert($users);
    }
}
