<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MobileNoPool;
use Faker\Generator as Faker;

$factory->define(MobileNoPool::class, function (Faker $faker) {

    return [
        'mobileNo' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'isRoaming' => $faker->randomDigitNotNull,
        'isIDD' => $faker->randomDigitNotNull,
        'mobilePlan' => $faker->randomDigitNotNull,
        'isMobileDataActivated' => $faker->randomDigitNotNull,
        'isDataRoaming' => $faker->randomDigitNotNull,
        'DataLimit' => $faker->word,
        'isAssigned' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
