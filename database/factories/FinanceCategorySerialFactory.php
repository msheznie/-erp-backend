<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FinanceCategorySerial;
use Faker\Generator as Faker;

$factory->define(FinanceCategorySerial::class, function (Faker $faker) {

    return [
        'faFinanceCatID' => $faker->randomDigitNotNull,
        'lastSerialNo' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull
    ];
});
