<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BudgetMasterRefferedHistory;
use Faker\Generator as Faker;

$factory->define(BudgetMasterRefferedHistory::class, function (Faker $faker) {

    return [
        'budgetmasterID' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'companyFinanceYearID' => $faker->randomDigitNotNull,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'serviceLineCode' => $faker->word,
        'templateMasterID' => $faker->randomDigitNotNull,
        'Year' => $faker->randomDigitNotNull,
        'month' => $faker->randomDigitNotNull,
        'generateStatus' => $faker->randomDigitNotNull,
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedByEmpSystemID' => $faker->randomDigitNotNull,
        'confirmedByEmpID' => $faker->word,
        'confirmedByEmpName' => $faker->word,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedYN' => $faker->randomDigitNotNull,
        'approvedByUserID' => $faker->randomDigitNotNull,
        'approvedByUserSystemID' => $faker->randomDigitNotNull,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'RollLevForApp_curr' => $faker->randomDigitNotNull,
        'createdByUserSystemID' => $faker->randomDigitNotNull,
        'createdByUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'modifiedUser' => $faker->word,
        'modifiedPc' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'refferedBackYN' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull
    ];
});
