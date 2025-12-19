<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\NotificationScenarios;
use Faker\Generator as Faker;

$factory->define(NotificationScenarios::class, function (Faker $faker) {

    return [
        'moduleID' => $faker->randomDigitNotNull,
        'scenarioDescription' => $faker->word,
        'comment' => $faker->word,
        'isActive' => $faker->randomDigitNotNull,
        'dayCheckYN' => $faker->randomDigitNotNull,
        'createdBy' => $faker->randomDigitNotNull,
        'updatedBy' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
