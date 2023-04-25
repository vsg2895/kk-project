<?php

use Faker\Generator as Faker;
use Jakten\Models\Comment;

$factory->define(Comment::class, function (Faker $faker) {
    $faker->locale('sv_SE');

    return [
        'user_id' => $faker->numberBetween(1, 17),
        'post_id' => $faker->numberBetween(1, 25),
        'text' => $faker->sentence,
    ];
});
