<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FcmToken;
use Faker\Generator as Faker;

$factory->define(FcmToken::class, function (Faker $faker) {

    return [
        'userID' => $faker->randomDigitNotNull,
        'fcm_token' => $faker->word
    ];
});
