<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VatReturnFillingCategory;
use Faker\Generator as Faker;

$factory->define(VatReturnFillingCategory::class, function (Faker $faker) {

    return [
        'category' => $faker->text,
        'masterID' => $faker->randomDigitNotNull,
        'isActive' => $faker->randomDigitNotNull
    ];
});
