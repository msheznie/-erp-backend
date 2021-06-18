<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CurrencyConversionDetail;
use Faker\Generator as Faker;

$factory->define(CurrencyConversionDetail::class, function (Faker $faker) {

    return [
        'currencyConversioMasterID' => $faker->randomDigitNotNull,
        'masterCurrencyID' => $faker->randomDigitNotNull,
        'subCurrencyID' => $faker->randomDigitNotNull,
        'conversion' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
