<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TaxMaster;
use Faker\Generator as Faker;

$factory->define(TaxMaster::class, function (Faker $faker) {

    return [
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'taxShortCode' => $faker->word,
        'taxDescription' => $faker->word,
        'taxPercent' => $faker->randomDigitNotNull,
        'payeeSystemCode' => $faker->randomDigitNotNull,
        'taxType' => $faker->randomDigitNotNull,
        'selectForPayment' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
