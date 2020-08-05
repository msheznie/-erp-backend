<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TaxVatMainCategories;
use Faker\Generator as Faker;

$factory->define(TaxVatMainCategories::class, function (Faker $faker) {

    return [
        'taxMasterAutoID' => $faker->randomDigitNotNull,
        'mainCategoryDescription' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
