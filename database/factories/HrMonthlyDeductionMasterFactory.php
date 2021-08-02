<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HrMonthlyDeductionMaster;
use Faker\Generator as Faker;

$factory->define(HrMonthlyDeductionMaster::class, function (Faker $faker) {

    return [
        'monthlyDeductionCode' => $faker->word,
        'serialNo' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'payrollGroup' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'currencyID' => $faker->randomDigitNotNull,
        'currency' => $faker->word,
        'dateMD' => $faker->word,
        'isNonPayroll' => $faker->word,
        'isProcessed' => $faker->randomDigitNotNull,
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedByEmpID' => $faker->word,
        'confirmedByName' => $faker->word,
        'confirmedDate' => $faker->word,
        'currentApprovalLevel' => $faker->randomDigitNotNull,
        'approvedYN' => $faker->randomDigitNotNull,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'currentLevelNo' => $faker->randomDigitNotNull,
        'approvedbyEmpID' => $faker->word,
        'approvedbyEmpName' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'segmentID' => $faker->randomDigitNotNull,
        'segmentCode' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
