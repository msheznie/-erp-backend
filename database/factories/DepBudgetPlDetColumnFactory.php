<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DepBudgetPlDetColumn;
use Faker\Generator as Faker;

$factory->define(DepBudgetPlDetColumn::class, function (Faker $faker) {

    return [
        'columnName' => $faker->word,
        'slug' => $faker->word,
        'isDefault' => $faker->randomDigitNotNull
    ];
});
