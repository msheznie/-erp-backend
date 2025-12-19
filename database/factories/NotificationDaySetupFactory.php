<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\NotificationDaySetup;
use Faker\Generator as Faker;

$factory->define(NotificationDaySetup::class, function (Faker $faker) {

    return [
        'companyScenarionID' => $faker->randomDigitNotNull,
        'beforeAfter' => $faker->randomDigitNotNull,
        'days' => $faker->randomDigitNotNull,
        'isActive' => $faker->randomDigitNotNull,
        'createdBy' => $faker->randomDigitNotNull,
        'updatedBy' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
