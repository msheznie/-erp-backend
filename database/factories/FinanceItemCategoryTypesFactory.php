<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FinanceItemCategoryTypes;
use Faker\Generator as Faker;

$factory->define(FinanceItemCategoryTypes::class, function (Faker $faker) {

    return [
        'itemCategorySubID' => $faker->randomDigitNotNull,
        'categoryTypeID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
