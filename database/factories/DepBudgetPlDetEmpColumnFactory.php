<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DepBudgetPlDetEmpColumn;
use Faker\Generator as Faker;

$factory->define(DepBudgetPlDetEmpColumn::class, function (Faker $faker) {

    return [
        'companySystemID' => $faker->randomDigitNotNull,
        'empID' => $faker->randomDigitNotNull,
        'columnID' => $faker->word
    ];
});
