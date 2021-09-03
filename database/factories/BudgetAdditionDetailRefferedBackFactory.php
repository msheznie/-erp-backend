<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BudgetAdditionDetailRefferedBack;
use Faker\Generator as Faker;

$factory->define(BudgetAdditionDetailRefferedBack::class, function (Faker $faker) {

    return [
        'id' => $faker->randomDigitNotNull,
        'budgetAdditionFormAutoID' => $faker->randomDigitNotNull,
        'year' => $faker->randomDigitNotNull,
        'templateDetailID' => $faker->randomDigitNotNull,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'serviceLineCode' => $faker->word,
        'budjetDetailsID' => $faker->randomDigitNotNull,
        'chartOfAccountSystemID' => $faker->randomDigitNotNull,
        'gLCode' => $faker->word,
        'gLCodeDescription' => $faker->word,
        'adjustmentAmountLocal' => $faker->randomDigitNotNull,
        'adjustmentAmountRpt' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'remarks' => $faker->text,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'createdDateTime' => $faker->date('Y-m-d H:i:s')
    ];
});
