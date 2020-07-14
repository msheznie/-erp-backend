<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MobileBillMaster;
use Faker\Generator as Faker;

$factory->define(MobileBillMaster::class, function (Faker $faker) {

    return [
        'billPeriod' => $faker->randomDigitNotNull,
        'mobilebillmasterCode' => $faker->word,
        'serialNo' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'companyID' => $faker->word,
        'Description' => $faker->word,
        'createDate' => $faker->date('Y-m-d H:i:s'),
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'confirmedby' => $faker->word,
        'confirmedByEmployeeSystemID' => $faker->randomDigitNotNull,
        'approvedby' => $faker->word,
        'approvedbyEmployeeSystemID' => $faker->randomDigitNotNull,
        'ApprovedYN' => $faker->randomDigitNotNull,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'createUserID' => $faker->word,
        'createPCID' => $faker->word,
        'modifiedpc' => $faker->word,
        'modifiedUser' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
