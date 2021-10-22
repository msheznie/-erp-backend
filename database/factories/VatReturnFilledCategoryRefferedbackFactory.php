<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VatReturnFilledCategoryRefferedback;
use Faker\Generator as Faker;

$factory->define(VatReturnFilledCategoryRefferedback::class, function (Faker $faker) {

    return [
        'returnFilledCategoryID' => $faker->randomDigitNotNull,
        'categoryID' => $faker->randomDigitNotNull,
        'vatReturnFillingID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
