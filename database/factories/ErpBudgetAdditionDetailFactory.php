<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ErpBudgetAdditionDetail;
use Faker\Generator as Faker;

$factory->define(ErpBudgetAdditionDetail::class, function (Faker $faker) {

    return [
        'budgetTransferFormAutoID' => $faker->randomDigitNotNull,
        'year' => $faker->randomDigitNotNull,
        'fromTemplateDetailID' => $faker->randomDigitNotNull,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'serviceLineCode' => $faker->word,
        'chartOfAccountSystemID' => $faker->randomDigitNotNull,
        'gLCode' => $faker->word,
        'gLCodeDescription' => $faker->word,
        'adjustmentAmountLocal' => $faker->randomDigitNotNull,
        'adjustmentAmountRpt' => $faker->randomDigitNotNull,
        'remarks' => $faker->text,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
