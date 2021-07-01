<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BudgetReviewTransferAddition;
use Faker\Generator as Faker;

$factory->define(BudgetReviewTransferAddition::class, function (Faker $faker) {

    return [
        'budgetTransferAdditionID' => $faker->randomDigitNotNull,
        'budgetTransferType' => $faker->randomDigitNotNull,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull
    ];
});
