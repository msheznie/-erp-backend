<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ContingencyBudgetRefferedBack;
use Faker\Generator as Faker;

$factory->define(ContingencyBudgetRefferedBack::class, function (Faker $faker) {

    return [
        'ID' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'companyFinanceYearID' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'serialNo' => $faker->randomDigitNotNull,
        'contingencyBudgetNo' => $faker->word,
        'currencyID' => $faker->randomDigitNotNull,
        'contigencyAmount' => $faker->randomDigitNotNull,
        'year' => $faker->randomDigitNotNull,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'createdDate' => $faker->date('Y-m-d H:i:s'),
        'comments' => $faker->text,
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'confirmedByEmpSystemID' => $faker->randomDigitNotNull,
        'confirmedByEmpID' => $faker->word,
        'confirmedByEmpName' => $faker->word,
        'approvedYN' => $faker->randomDigitNotNull,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedByUserSystemID' => $faker->randomDigitNotNull,
        'approvedEmpID' => $faker->word,
        'approvedEmpName' => $faker->word,
        'timesReferred' => $faker->randomDigitNotNull,
        'refferedBackYN' => $faker->randomDigitNotNull,
        'RollLevForApp_curr' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdPcID' => $faker->word,
        'modifiedPc' => $faker->word,
        'modifiedUser' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'budgetID' => $faker->randomDigitNotNull,
        'templateMasterID' => $faker->randomDigitNotNull,
        'contingencyPercentage' => $faker->randomDigitNotNull,
        'budgetAmount' => $faker->randomDigitNotNull
    ];
});
