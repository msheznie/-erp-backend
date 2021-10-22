<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VatReturnFillingDetail;
use Faker\Generator as Faker;

$factory->define(VatReturnFillingDetail::class, function (Faker $faker) {

    return [
        'vatReturnFilledCategoryID' => $faker->randomDigitNotNull,
        'vatReturnFillingID' => $faker->randomDigitNotNull,
        'vatReturnFillingSubCatgeoryID' => $faker->randomDigitNotNull,
        'taxAmount' => $faker->randomDigitNotNull,
        'taxableAmount' => $faker->randomDigitNotNull
    ];
});
