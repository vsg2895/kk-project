<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSchoolRatingVerifiedNotifiedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_ratings', function (Blueprint $table) {
            $table->timestamp('school_notified')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_ratings', function (Blueprint $table) {
            $table->dropColumn('school_notified');
        });
    }
}
