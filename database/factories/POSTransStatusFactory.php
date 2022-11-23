<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSTransStatus;
use Faker\Generator as Faker;

$factory->define(POSTransStatus::class, function (Faker $faker) {

    return [
        'description' => $faker->word
    ];
});
