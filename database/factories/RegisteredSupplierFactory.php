<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RegisteredSupplier;
use Faker\Generator as Faker;

$factory->define(RegisteredSupplier::class, function (Faker $faker) {

    return [
        'supplierName' => $faker->word,
        'telephone' => $faker->word,
        'supEmail' => $faker->word,
        'supplierCountryID' => $faker->randomDigitNotNull,
        'registrationExprity' => $faker->date('Y-m-d H:i:s'),
        'currency' => $faker->randomDigitNotNull,
        'nameOnPaymentCheque' => $faker->word,
        'address' => $faker->text,
        'fax' => $faker->word,
        'webAddress' => $faker->word,
        'registrationNumber' => $faker->word,
        'createdDate' => $faker->date('Y-m-d H:i:s')
    ];
});
