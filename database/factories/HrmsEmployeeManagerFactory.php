<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HrmsEmployeeManager;
use Faker\Generator as Faker;

$factory->define(HrmsEmployeeManager::class, function (Faker $faker) {

    return [
        'empID' => $faker->randomDigitNotNull,
        'managerID' => $faker->randomDigitNotNull,
        'level' => $faker->randomDigitNotNull,
        'active' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdDate' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserID' => $faker->word,
        'modifiedDate' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
