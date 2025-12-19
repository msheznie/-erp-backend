<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ExternalLinkHash;
use Faker\Generator as Faker;

$factory->define(ExternalLinkHash::class, function (Faker $faker) {

    return [
        'hashKey' => $faker->word,
        'generatedBy' => $faker->randomDigitNotNull,
        'genratedDate' => $faker->date('Y-m-d H:i:s'),
        'expiredIn' => $faker->date('Y-m-d H:i:s'),
        'isUsed' => $faker->randomDigitNotNull
    ];
});
