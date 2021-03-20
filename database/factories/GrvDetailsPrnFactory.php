<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\GrvDetailsPrn;
use Faker\Generator as Faker;

$factory->define(GrvDetailsPrn::class, function (Faker $faker) {

    return [
        'grvDetailsID' => $faker->randomDigitNotNull,
        'purhasereturnDetailID' => $faker->randomDigitNotNull,
        'prnQty' => $faker->randomDigitNotNull
    ];
});
