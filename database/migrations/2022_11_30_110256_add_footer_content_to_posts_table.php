<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFooterContentToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('post_type')->after('title')->default('post');
            $table->text('footer_content')->after('content')->nullable();
            $table->boolean('hidden')->after('status')->default(false)->comment('Hide from google search');
            $table->string('slug', 100)->after('title')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'footer_content',
                'post_type',
                'hidden',
            ]);
        });
    }
}
