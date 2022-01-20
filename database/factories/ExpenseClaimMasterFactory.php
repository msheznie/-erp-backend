<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ExpenseClaimMaster;
use Faker\Generator as Faker;

$factory->define(ExpenseClaimMaster::class, function (Faker $faker) {

    return [
        'documentID' => $faker->word,
        'serialNo' => $faker->randomDigitNotNull,
        'expenseClaimCode' => $faker->word,
        'expenseClaimDate' => $faker->word,
        'claimedByEmpID' => $faker->randomDigitNotNull,
        'claimedByEmpName' => $faker->word,
        'comments' => $faker->text,
        'isCRM' => $faker->randomDigitNotNull,
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedByEmpID' => $faker->randomDigitNotNull,
        'confirmedByName' => $faker->word,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedYN' => $faker->randomDigitNotNull,
        'approvedByEmpID' => $faker->randomDigitNotNull,
        'approvedByEmpName' => $faker->word,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'approvalComments' => $faker->word,
        'glCodeAssignedYN' => $faker->randomDigitNotNull,
        'addedForPayment' => $faker->randomDigitNotNull,
        'addedToSalary' => $faker->randomDigitNotNull,
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
