<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Movie;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Movie::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->text,
        'release_date' => $faker->dateTimeBetween('now', '+30 years')->format('d.m.Y'),
    ];
});
