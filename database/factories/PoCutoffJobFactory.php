<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PoCutoffJob;
use Faker\Generator as Faker;

$factory->define(PoCutoffJob::class, function (Faker $faker) {

    return [
        'poCount' => $faker->randomDigitNotNull,
        'jobCount' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
