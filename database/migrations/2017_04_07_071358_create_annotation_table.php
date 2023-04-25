<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('type')->nullable();
            $table->json('data')->nullable();
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('organization_annotations', function (Blueprint $table) {
            $table->integer('organization_id')->unsigned();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');

            $table->integer('annotation_id')->unsigned();
            $table->foreign('annotation_id')->references('id')->on('annotations')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('school_annotations', function (Blueprint $table) {
            $table->integer('school_id')->unsigned();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');

            $table->integer('annotation_id')->unsigned();
            $table->foreign('annotation_id')->references('id')->on('annotations')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('annotation');
        Schema::dropIfExists('organization_annotations');
        Schema::dropIfExists('school_annotations');
    }
}
