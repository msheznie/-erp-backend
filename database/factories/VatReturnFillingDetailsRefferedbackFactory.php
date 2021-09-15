<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VatReturnFillingDetailsRefferedback;
use Faker\Generator as Faker;

$factory->define(VatReturnFillingDetailsRefferedback::class, function (Faker $faker) {

    return [
        'returnFillingDetailID' => $faker->randomDigitNotNull,
        'vatReturnFilledCategoryID' => $faker->randomDigitNotNull,
        'vatReturnFillingID' => $faker->randomDigitNotNull,
        'vatReturnFillingSubCatgeoryID' => $faker->randomDigitNotNull,
        'taxAmount' => $faker->randomDigitNotNull,
        'taxableAmount' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
