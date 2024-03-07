<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RecurringVoucherSetup;
use Faker\Generator as Faker;

$factory->define(RecurringVoucherSetup::class, function (Faker $faker) {

    return [
        'schedule' => $faker->randomDigitNotNull,
        'startDate' => $faker->date('Y-m-d H:i:s'),
        'endDate' => $faker->date('Y-m-d H:i:s'),
        'noOfDayMonthYear' => $faker->randomDigitNotNull,
        'processDate' => $faker->date('Y-m-d H:i:s'),
        'documentStatus' => $faker->randomDigitNotNull,
        'currencyID' => $faker->randomDigitNotNull,
        'documentType' => $faker->randomDigitNotNull,
        'narration' => $faker->text,
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedByEmpSystemID' => $faker->randomDigitNotNull,
        'confirmedByEmpID' => $faker->randomDigitNotNull,
        'confirmedByName' => $faker->text,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'approved' => $faker->randomDigitNotNull,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedByUserID' => $faker->randomDigitNotNull,
        'approvedByUserSystemID' => $faker->randomDigitNotNull,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->randomDigitNotNull,
        'createdPcID' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'modifiedUser' => $faker->word,
        'modifiedPc' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
