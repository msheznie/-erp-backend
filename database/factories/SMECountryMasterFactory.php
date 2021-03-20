<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMECountryMaster;
use Faker\Generator as Faker;

$factory->define(SMECountryMaster::class, function (Faker $faker) {

    return [
        'countryShortCode' => $faker->word,
        'CountryDes' => $faker->word,
        'Nationality' => $faker->word,
        'countryCode' => $faker->randomDigitNotNull,
        'countryTimeZone' => $faker->word
    ];
});
