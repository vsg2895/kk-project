<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('school_id');
            $table->string('file_name')->unique();
            $table->string('alt_text');
            $table->timestamps();

            $table->foreign('school_id')
                ->references('id')->on('schools')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_images');
    }
}
