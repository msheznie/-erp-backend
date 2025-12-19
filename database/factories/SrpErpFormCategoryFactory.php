<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SrpErpFormCategory;
use Faker\Generator as Faker;

$factory->define(SrpErpFormCategory::class, function (Faker $faker) {

    return [
        'Category' => $faker->word,
        'navigationMenuID' => $faker->randomDigitNotNull
    ];
});
