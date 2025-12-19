<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BudgetTransferFormRefferedBack;
use Faker\Generator as Faker;

$factory->define(BudgetTransferFormRefferedBack::class, function (Faker $faker) {

    return [
        'templatesMasterAutoID' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'budgetTransferFormAutoID' => $faker->randomDigitNotNull,
        'serialNo' => $faker->randomDigitNotNull,
        'year' => $faker->randomDigitNotNull,
        'refferedBackYN' => $faker->randomDigitNotNull,
        'transferVoucherNo' => $faker->word,
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
        'RollLevForApp_curr' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdPcID' => $faker->word,
        'modifiedPc' => $faker->word,
        'modifiedUser' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
