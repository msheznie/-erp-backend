<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ExampleTableTemplate;
use Faker\Generator as Faker;

$factory->define(ExampleTableTemplate::class, function (Faker $faker) {

    return [
        'documentSystemID' => $faker->randomDigitNotNull,
        'data' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s')
    ];
});
