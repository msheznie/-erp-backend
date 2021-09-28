<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\LeaveGroupDetails;
use Faker\Generator as Faker;

$factory->define(LeaveGroupDetails::class, function (Faker $faker) {

    return [
        'leaveGroupID' => $faker->randomDigitNotNull,
        'leaveTypeID' => $faker->randomDigitNotNull,
        'policyMasterID' => $faker->randomDigitNotNull,
        'isDailyBasisAccrual' => $faker->word,
        'noOfDays' => $faker->randomDigitNotNull,
        'isAllowminus' => $faker->randomDigitNotNull,
        'isAllowminusdays' => $faker->randomDigitNotNull,
        'isCalenderDays' => $faker->randomDigitNotNull,
        'stretchDays' => $faker->randomDigitNotNull,
        'isCarryForward' => $faker->randomDigitNotNull,
        'maxCarryForward' => $faker->randomDigitNotNull,
        'maxOccurrenceYN' => $faker->word,
        'noofOccurrence' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'noOfHours' => $faker->randomDigitNotNull,
        'noOfHourscompleted' => $faker->randomDigitNotNull
    ];
});
