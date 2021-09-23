<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\LeaveAccrualDetail;
use Faker\Generator as Faker;

$factory->define(LeaveAccrualDetail::class, function (Faker $faker) {

    return [
        'leaveaccrualMasterID' => $faker->randomDigitNotNull,
        'empID' => $faker->randomDigitNotNull,
        'leavePeriod' => $faker->randomDigitNotNull,
        'comment' => $faker->text,
        'leaveGroupID' => $faker->randomDigitNotNull,
        'leaveType' => $faker->randomDigitNotNull,
        'daysEntitled' => $faker->randomDigitNotNull,
        'hoursEntitled' => $faker->randomDigitNotNull,
        'previous_balance' => $faker->randomDigitNotNull,
        'carryForwardDays' => $faker->randomDigitNotNull,
        'maxCarryForwardDays' => $faker->randomDigitNotNull,
        'description' => $faker->text,
        'calendarHolidayID' => $faker->randomDigitNotNull,
        'leaveMasterID' => $faker->randomDigitNotNull,
        'cancelledLeaveMasterID' => $faker->randomDigitNotNull,
        'createDate' => $faker->date('Y-m-d H:i:s'),
        'createdUserGroup' => $faker->word,
        'createdPCid' => $faker->word,
        'modifiedUser' => $faker->word,
        'modifiedPc' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'manualYN' => $faker->word,
        'initalDate' => $faker->randomDigitNotNull,
        'nextDate' => $faker->randomDigitNotNull,
        'policyMasterID' => $faker->randomDigitNotNull
    ];
});
