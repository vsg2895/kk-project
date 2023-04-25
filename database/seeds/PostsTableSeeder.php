<?php

use Illuminate\Database\Seeder;
use Jakten\Models\Post;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Post::class, 25)->create();
    }
}
