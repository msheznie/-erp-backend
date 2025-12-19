<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\WeekDays;
use Faker\Generator as Faker;

$factory->define(WeekDays::class, function (Faker $faker) {

    return [
        'description' => $faker->word
    ];
});
