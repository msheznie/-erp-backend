<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CalendarDatesDetailEditLog;
use Faker\Generator as Faker;

$factory->define(CalendarDatesDetailEditLog::class, function (Faker $faker) {

    return [
        'calendar_date_id' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'from_date' => $faker->date('Y-m-d H:i:s'),
        'master_id' => $faker->randomDigitNotNull,
        'modify_type' => $faker->randomDigitNotNull,
        'ref_log_id' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'to_date' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'version_id' => $faker->randomDigitNotNull
    ];
});
