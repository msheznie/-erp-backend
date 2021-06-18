<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CurrencyConversionMaster;
use Faker\Generator as Faker;

$factory->define(CurrencyConversionMaster::class, function (Faker $faker) {

    return [
        'conversionCode' => $faker->word,
        'conversionDate' => $faker->date('Y-m-d H:i:s'),
        'createdBy' => $faker->word,
        'description' => $faker->text,
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedEmpName' => $faker->word,
        'ConfirmedBy' => $faker->word,
        'ConfirmedBySystemID' => $faker->randomDigitNotNull,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedYN' => $faker->randomDigitNotNull,
        'approvedby' => $faker->word,
        'approvedEmpSystemID' => $faker->randomDigitNotNull,
        'refferedBackYN' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'RollLevForApp_curr' => $faker->randomDigitNotNull,
        'timeStamp' => $faker->date('Y-m-d H:i:s')
    ];
});
