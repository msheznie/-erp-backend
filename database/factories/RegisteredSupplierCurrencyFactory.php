<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RegisteredSupplierCurrency;
use Faker\Generator as Faker;

$factory->define(RegisteredSupplierCurrency::class, function (Faker $faker) {

    return [
        'registeredSupplierID' => $faker->randomDigitNotNull,
        'currencyID' => $faker->randomDigitNotNull,
        'isAssigned' => $faker->randomDigitNotNull,
        'isDefault' => $faker->randomDigitNotNull
    ];
});
