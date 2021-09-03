<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\NotificationUserDayCheck;
use Faker\Generator as Faker;

$factory->define(NotificationUserDayCheck::class, function (Faker $faker) {

    return [
        'notificationUserID' => $faker->randomDigitNotNull,
        'notificationDaySetupID' => $faker->randomDigitNotNull,
        'pushNotification' => $faker->randomDigitNotNull,
        'emailNotification' => $faker->randomDigitNotNull,
        'webNotification' => $faker->randomDigitNotNull,
        'createdBy' => $faker->randomDigitNotNull,
        'updatedBy' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
