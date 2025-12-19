<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\LeaveAccrualMaster;
use Faker\Generator as Faker;

$factory->define(LeaveAccrualMaster::class, function (Faker $faker) {

    return [
        'documentID' => $faker->word,
        'leaveGroupID' => $faker->randomDigitNotNull,
        'leaveaccrualMasterCode' => $faker->word,
        'description' => $faker->text,
        'policyMasterID' => $faker->randomDigitNotNull,
        'company_finance_year_id' => $faker->randomDigitNotNull,
        'dailyAccrualYN' => $faker->word,
        'dailyAccrualDate' => $faker->word,
        'year' => $faker->randomDigitNotNull,
        'manualYN' => $faker->randomDigitNotNull,
        'month' => $faker->randomDigitNotNull,
        'calendarHolidayID' => $faker->randomDigitNotNull,
        'cancelledLeaveMasterID' => $faker->randomDigitNotNull,
        'leaveMasterID' => $faker->randomDigitNotNull,
        'adjustmentType' => $faker->randomDigitNotNull,
        'isHourly' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'serialNo' => $faker->randomDigitNotNull,
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedby' => $faker->word,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedYN' => $faker->randomDigitNotNull,
        'approvedby' => $faker->word,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'createdUserID' => $faker->randomDigitNotNull,
        'createdUserGroup' => $faker->word,
        'createDate' => $faker->date('Y-m-d H:i:s'),
        'createdpc' => $faker->word,
        'modifieduser' => $faker->word,
        'modifiedpc' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
