<?php

use Illuminate\Database\Seeder;
use Jakten\Models\Comment;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Comment::class, 40)->create();
    }
}
