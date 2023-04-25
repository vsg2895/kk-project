<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('org_number')->unique()->nullable();
            $table->string('payment_id')->unique()->nullable();
            $table->string('payment_secret')->unique()->nullable();
            $table->string('external_sign_up_id')->unique()->nullable();
            $table->string('sign_up_status')->default(\Jakten\Helpers\KlarnaSignup::STATUS_NOT_INITIATED);
            $table->string('sign_up_rejected_reason')->nullable();
            $table->text('sign_up_status_text')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('organizations');
    }
}
