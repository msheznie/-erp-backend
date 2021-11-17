<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ExpenseAssetAllocation;
use Faker\Generator as Faker;

$factory->define(ExpenseAssetAllocation::class, function (Faker $faker) {

    return [
        'assetID' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'amount' => $faker->randomDigitNotNull
    ];
});
