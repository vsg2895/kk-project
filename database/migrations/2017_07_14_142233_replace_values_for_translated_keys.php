<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReplaceValuesForTranslatedKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $events = [
            'new_order' => 'New order',
            'new_register' => 'New registration',
            'order_cancelled' => 'Order canceled',
            'order_failed' => 'Order failed'
        ];

        foreach ($events as $key => $event) {
            DB::table('notify_events')
                ->where('label', $event)
                ->update([
                    'label' => $key
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $events = [
            'new_order' => 'New order',
            'new_register' => 'New registration',
            'order_cancelled' => 'Order canceled',
            'order_failed' => 'Order failed'
        ];

        foreach ($events as $key => $event) {
            DB::table('notify_events')
                ->where('label', $key)
                ->update([
                    'label' => $event
                ]);
        }
    }
}
