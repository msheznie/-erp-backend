<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMELaveType;
use Faker\Generator as Faker;

$factory->define(SMELaveType::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'policyID' => $faker->randomDigitNotNull,
        'isPaidLeave' => $faker->randomDigitNotNull,
        'isPlanApplicable' => $faker->randomDigitNotNull,
        'isAnnualLeave' => $faker->randomDigitNotNull,
        'isEmergencyLeave' => $faker->randomDigitNotNull,
        'isSickLeave' => $faker->randomDigitNotNull,
        'isShortLeave' => $faker->randomDigitNotNull,
        'shortLeaveMaxHours' => $faker->randomDigitNotNull,
        'shortLeaveMaxMins' => $faker->randomDigitNotNull,
        'sortOrder' => $faker->randomDigitNotNull,
        'typeConfirmed' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'attachmentRequired' => $faker->randomDigitNotNull
    ];
});
