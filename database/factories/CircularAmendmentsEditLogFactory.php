<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CircularAmendmentsEditLog;
use Faker\Generator as Faker;

$factory->define(CircularAmendmentsEditLog::class, function (Faker $faker) {

    return [
        'amendment_id' => $faker->randomDigitNotNull,
        'circular_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'master_id' => $faker->randomDigitNotNull,
        'modify_type' => $faker->randomDigitNotNull,
        'ref_log_id' => $faker->randomDigitNotNull,
        'status' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'vesion_id' => $faker->randomDigitNotNull
    ];
});
