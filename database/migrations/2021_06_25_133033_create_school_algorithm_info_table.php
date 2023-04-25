<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolAlgorithmInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_algorithm_info', function (Blueprint $table) {
            $table->integer('school_id');
            $table->integer('courses');
            $table->integer('sum_seats');
            $table->integer('canceled');
            $table->integer('conversion');
            $table->float('rating');
            $table->integer('avg_price');
            $table->primary(['school_id']);
            $table->timestamps();
        });

        Schema::create('search_algorithm_config', function (Blueprint $table) {
            $table->integer('courses');
            $table->integer('sum_seats');
            $table->integer('canceled');
            $table->integer('conversion');
            $table->integer('avg_price');
            $table->integer('rating');
            $table->integer('user_id');
            $table->timestamp('created_at')->useCurrent();
        });

        DB::table('search_algorithm_config')->insert(
            ['courses' => 20,  'sum_seats' => 20,  'canceled' => 20,  'conversion' => 20,  'rating' => 20, 'user_id' => 1]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_algorithm_info');
        Schema::dropIfExists('search_algorithm_config');
    }
}
