<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BudgetDetailsRefferedHistory;
use Faker\Generator as Faker;

$factory->define(BudgetDetailsRefferedHistory::class, function (Faker $faker) {

    return [
        'budjetDetailsID' => $faker->randomDigitNotNull,
        'budgetmasterID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyId' => $faker->word,
        'companyFinanceYearID' => $faker->randomDigitNotNull,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'serviceLine' => $faker->word,
        'templateDetailID' => $faker->randomDigitNotNull,
        'chartOfAccountID' => $faker->randomDigitNotNull,
        'glCode' => $faker->word,
        'glCodeType' => $faker->word,
        'Year' => $faker->randomDigitNotNull,
        'month' => $faker->randomDigitNotNull,
        'budjetAmtLocal' => $faker->randomDigitNotNull,
        'budjetAmtRpt' => $faker->randomDigitNotNull,
        'createdByUserSystemID' => $faker->randomDigitNotNull,
        'createdByUserID' => $faker->word,
        'modifiedByUserSystemID' => $faker->randomDigitNotNull,
        'modifiedByUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
