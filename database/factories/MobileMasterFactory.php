<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MobileMaster;
use Faker\Generator as Faker;

$factory->define(MobileMaster::class, function (Faker $faker) {

    return [
        'empID' => $faker->word,
        'employeeSystemID' => $faker->randomDigitNotNull,
        'assignDate' => $faker->word,
        'mobileNoPoolID' => $faker->randomDigitNotNull,
        'mobileNo' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'currentPlan' => $faker->randomDigitNotNull,
        'isIDDActive' => $faker->randomDigitNotNull,
        'isRoamingActive' => $faker->randomDigitNotNull,
        'currency' => $faker->randomDigitNotNull,
        'creditlimit' => $faker->randomDigitNotNull,
        'isDataRoaming' => $faker->randomDigitNotNull,
        'isActive' => $faker->randomDigitNotNull,
        'datedeactivated' => $faker->date('Y-m-d H:i:s'),
        'recoverYN' => $faker->randomDigitNotNull,
        'isInternetSim' => $faker->randomDigitNotNull,
        'createDate' => $faker->date('Y-m-d H:i:s'),
        'createUserID' => $faker->word,
        'createPCID' => $faker->word,
        'modifiedpc' => $faker->word,
        'modifiedUser' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
