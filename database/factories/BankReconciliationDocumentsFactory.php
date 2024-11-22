<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BankReconciliationDocuments;
use Faker\Generator as Faker;

$factory->define(BankReconciliationDocuments::class, function (Faker $faker) {

    return [
        'bankRecAutoID' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentAutoId' => $faker->randomDigitNotNull
    ];
});
