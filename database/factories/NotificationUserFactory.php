<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\NotificationUser;
use Faker\Generator as Faker;

$factory->define(NotificationUser::class, function (Faker $faker) {

    return [
        'empID' => $faker->randomDigitNotNull,
        'companyScenarionID' => $faker->randomDigitNotNull,
        'applicableCategoryID' => $faker->randomDigitNotNull,
        'isActive' => $faker->randomDigitNotNull,
        'createdBy' => $faker->randomDigitNotNull,
        'updatedBy' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
