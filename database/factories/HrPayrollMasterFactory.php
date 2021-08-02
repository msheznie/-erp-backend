<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HrPayrollMaster;
use Faker\Generator as Faker;

$factory->define(HrPayrollMaster::class, function (Faker $faker) {

    return [
        'documentID' => $faker->word,
        'documentCode' => $faker->word,
        'documentNo' => $faker->randomDigitNotNull,
        'payrollGroupID' => $faker->randomDigitNotNull,
        'periodID' => $faker->randomDigitNotNull,
        'payrollYear' => $faker->randomDigitNotNull,
        'payrollMonth' => $faker->randomDigitNotNull,
        'processDate' => $faker->word,
        'visibleDate' => $faker->word,
        'templateID' => $faker->randomDigitNotNull,
        'narration' => $faker->text,
        'isBankTransferProcessed' => $faker->randomDigitNotNull,
        'financialYearID' => $faker->randomDigitNotNull,
        'financialPeriodID' => $faker->randomDigitNotNull,
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedByEmpID' => $faker->randomDigitNotNull,
        'confirmedByName' => $faker->word,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedYN' => $faker->randomDigitNotNull,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'currentLevelNo' => $faker->randomDigitNotNull,
        'approvedbyEmpName' => $faker->word,
        'approvedbyEmpID' => $faker->word,
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
