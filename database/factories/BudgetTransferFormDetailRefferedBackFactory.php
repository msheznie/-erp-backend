<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BudgetTransferFormDetailRefferedBack;
use Faker\Generator as Faker;

$factory->define(BudgetTransferFormDetailRefferedBack::class, function (Faker $faker) {

    return [
        'budgetTransferFormDetailAutoID' => $faker->randomDigitNotNull,
        'budgetTransferFormAutoID' => $faker->randomDigitNotNull,
        'year' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'isFromContingency' => $faker->word,
        'contingencyBudgetID' => $faker->randomDigitNotNull,
        'fromTemplateDetailID' => $faker->randomDigitNotNull,
        'fromServiceLineSystemID' => $faker->randomDigitNotNull,
        'fromServiceLineCode' => $faker->word,
        'fromChartOfAccountSystemID' => $faker->randomDigitNotNull,
        'FromGLCode' => $faker->word,
        'FromGLCodeDescription' => $faker->word,
        'toTemplateDetailID' => $faker->randomDigitNotNull,
        'toServiceLineSystemID' => $faker->randomDigitNotNull,
        'toServiceLineCode' => $faker->word,
        'toChartOfAccountSystemID' => $faker->randomDigitNotNull,
        'toGLCode' => $faker->word,
        'toGLCodeDescription' => $faker->word,
        'adjustmentAmountLocal' => $faker->randomDigitNotNull,
        'adjustmentAmountRpt' => $faker->randomDigitNotNull,
        'remarks' => $faker->text,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
