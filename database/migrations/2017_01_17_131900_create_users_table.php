<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Jakten\Helpers\Roles;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->integer('role_id')->unsigned()->unique();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['name' => 'Admin', 'role_id' => Roles::ROLE_ADMIN],
            ['name' => 'Körskolelärare', 'role_id' => Roles::ROLE_ORGANIZATION_USER],
            ['name' => 'Student', 'role_id' => Roles::ROLE_STUDENT],
        ]);

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned()->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->string('given_name')->nullable()->default(null);
            $table->string('family_name')->nullable()->default(null);
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('password');
            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('role_id')->on('roles');
            $table->boolean('confirmed')->default(false);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('users')->insert([
            [
                'email' => 'frida@spiut.se',
                'given_name' => 'Frida',
                'family_name' => '',
                'role_id' => 3,
                'phone_number' => '',
                'confirmed' => true,
                'password' => bcrypt('jakten')
            ],
            [
                'email' => 'tony.carlsson@spiut.se',
                'given_name' => 'Tony',
                'family_name' => '',
                'role_id' => 3,
                'phone_number' => '',
                'confirmed' => true,
                'password' => bcrypt('jakten')
            ],
            [
                'email' => 'tomas@spiut.se',
                'given_name' => 'Tomas',
                'family_name' => '',
                'role_id' => 3,
                'phone_number' => '',
                'confirmed' => true,
                'password' => bcrypt('jakten')
            ],
            [
                'email' => 'olle@nectima.se',
                'given_name' => 'Olle',
                'family_name' => '',
                'role_id' => 3,
                'phone_number' => '',
                'confirmed' => true,
                'password' => bcrypt('jakten')
            ],
            [
                'email' => 'mimmi@korkortsjakten.se',
                'given_name' => 'Mimmi',
                'family_name' => '',
                'role_id' => 3,
                'phone_number' => '',
                'confirmed' => true,
                'password' => bcrypt('jakten')
            ],
            [
                'email' => 'nicolas@nectima.se',
                'given_name' => 'Nicolas',
                'family_name' => '',
                'role_id' => 3,
                'phone_number' => '',
                'confirmed' => true,
                'password' => bcrypt('jakten')
            ],
            [
                'email' => 'peter@nectima.se',
                'given_name' => 'Peter',
                'family_name' => '',
                'role_id' => 3,
                'phone_number' => '',
                'confirmed' => true,
                'password' => bcrypt('jakten')
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_role_id_foreign');
            $table->dropForeign('users_organization_id_foreign');
        });
        DB::table('roles')->delete();
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
    }
}
