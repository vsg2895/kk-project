<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifyEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notify_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('role_id')->on('roles')
                ->onUpdate('NO ACTION')
                ->onDelete('NO ACTION');
            $table->string('label', 190);
            $table->boolean('available');
            $table->timestamps();
        });

        $events = [
            [
                'role_id' => 0,
                'label' => 'New order',
                'available' => 1,
            ], [
                'role_id' => 0,
                'label' => 'New registration',
                'available' => 1
            ], [
                'role_id' => 0,
                'label' => 'Order canceled',
                'available' => 1
            ], [
                'role_id' => 0,
                'label' => 'Order failed',
                'available' => 1
            ]
        ];

        $userRoles = [
            1 => [0,2],
            2 => [0,2],
            3 => [0,1,2,3]
        ];

        foreach ($userRoles as $key => $keys) {
            $newEvents = array_intersect_key($events, array_flip($keys));
            foreach ($newEvents as &$event) {
                $event['role_id'] = $key;
            }
            DB::table('notify_events')->insert($newEvents);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notify_events');
    }
}
