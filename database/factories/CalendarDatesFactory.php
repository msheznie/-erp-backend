<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CalendarDates;
use Faker\Generator as Faker;

$factory->define(CalendarDates::class, function (Faker $faker) {

    return [
        'calendar_date' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull
    ];
});
