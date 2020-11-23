<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RegisteredSupplierContactDetail;
use Faker\Generator as Faker;

$factory->define(RegisteredSupplierContactDetail::class, function (Faker $faker) {

    return [
        'registeredSupplierID' => $faker->randomDigitNotNull,
        'contactTypeID' => $faker->randomDigitNotNull,
        'contactPersonName' => $faker->word,
        'contactPersonTelephone' => $faker->word,
        'contactPersonFax' => $faker->word,
        'contactPersonEmail' => $faker->word,
        'isDefault' => $faker->randomDigitNotNull
    ];
});
