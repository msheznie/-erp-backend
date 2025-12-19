<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EmployeeMobileBillMaster;
use Faker\Generator as Faker;

$factory->define(EmployeeMobileBillMaster::class, function (Faker $faker) {

    return [
        'mobilebillMasterID' => $faker->randomDigitNotNull,
        'companySysID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'employeeSystemID' => $faker->randomDigitNotNull,
        'empID' => $faker->word,
        'mobileNo' => $faker->randomDigitNotNull,
        'isSubmited' => $faker->randomDigitNotNull,
        'totalAmount' => $faker->randomDigitNotNull,
        'deductionAmount' => $faker->randomDigitNotNull,
        'exceededAmount' => $faker->randomDigitNotNull,
        'officialAmount' => $faker->randomDigitNotNull,
        'personalAmount' => $faker->randomDigitNotNull,
        'creditLimit' => $faker->randomDigitNotNull,
        'submittedBySysID' => $faker->randomDigitNotNull,
        'submittedby' => $faker->word,
        'submittedpc' => $faker->word,
        'createDate' => $faker->word,
        'createUserID' => $faker->word,
        'createPCID' => $faker->word,
        'modifiedpc' => $faker->word,
        'modifiedUser' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'approvedYN' => $faker->randomDigitNotNull,
        'approvedBySysID' => $faker->randomDigitNotNull,
        'approvedBy' => $faker->word,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'hrApprovedYN' => $faker->randomDigitNotNull,
        'hrApprovedBySystemID' => $faker->randomDigitNotNull,
        'hrApprovedBy' => $faker->word,
        'hrApprovedDate' => $faker->date('Y-m-d H:i:s'),
        'managerApprovedYN' => $faker->randomDigitNotNull,
        'managerApprovedBy' => $faker->word,
        'managerApprovedDate' => $faker->date('Y-m-d H:i:s'),
        'RollLevForApp_curr' => $faker->randomDigitNotNull,
        'isDeductedYN' => $faker->randomDigitNotNull
    ];
});
