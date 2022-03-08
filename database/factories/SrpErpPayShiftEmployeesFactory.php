<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SrpErpPayShiftEmployees;
use Faker\Generator as Faker;

$factory->define(SrpErpPayShiftEmployees::class, function (Faker $faker) {

    return [
        'shiftID' => $faker->randomDigitNotNull,
        'empID' => $faker->randomDigitNotNull,
        'startDate' => $faker->word,
        'endDate' => $faker->word,
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
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
