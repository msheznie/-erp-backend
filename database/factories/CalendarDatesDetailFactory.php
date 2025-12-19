<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CalendarDatesDetail;
use Faker\Generator as Faker;

$factory->define(CalendarDatesDetail::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'calendar_date_id' => $faker->randomDigitNotNull,
        'from_date' => $faker->date('Y-m-d H:i:s'),
        'to_date' => $faker->date('Y-m-d H:i:s'),
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull
    ];
});
