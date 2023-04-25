<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MergeSeatsAndSeatsAvailable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $courses = \DB::table('courses')->get();
        foreach ($courses as $course) {
            DB::table('courses')->where('id', $course->id)->update(['seats' => $course->seats_available]);
        }
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('seats_available');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('seats_available');
        });
        $courses = \DB::table('courses')->get();
        foreach ($courses as $course) {
            DB::table('courses')->where('id', $course->id)->update(['seats_available' => $course->seats]);
        }
    }
}
