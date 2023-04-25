<?php

use Faker\Generator as Faker;
use Jakten\Models\Post;

$factory->define(Post::class, function (Faker $faker) {
    $faker->locale('sv_SE');

    return [
        'user_id' => $faker->numberBetween(1, 7),
        'title'   => $faker->sentence,
        'content'    => $faker->text(1000),
        'status'  => $faker->numberBetween(0, 1),
    ];
});
