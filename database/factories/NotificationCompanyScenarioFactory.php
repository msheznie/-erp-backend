<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\NotificationCompanyScenario;
use Faker\Generator as Faker;

$factory->define(NotificationCompanyScenario::class, function (Faker $faker) {

    return [
        'scenarioID' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'isActive' => $faker->randomDigitNotNull,
        'createdBy' => $faker->randomDigitNotNull,
        'updatedBy' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
