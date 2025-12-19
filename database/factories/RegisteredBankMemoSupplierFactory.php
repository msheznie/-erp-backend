<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RegisteredBankMemoSupplier;
use Faker\Generator as Faker;

$factory->define(RegisteredBankMemoSupplier::class, function (Faker $faker) {

    return [
        'memoHeader' => $faker->word,
        'memoDetail' => $faker->word,
        'registeredSupplierID' => $faker->randomDigitNotNull,
        'supplierCurrencyID' => $faker->randomDigitNotNull,
        'bankMemoTypeID' => $faker->randomDigitNotNull
    ];
});
