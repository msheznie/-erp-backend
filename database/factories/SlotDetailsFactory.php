<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SlotDetails;
use Faker\Generator as Faker;

$factory->define(SlotDetails::class, function (Faker $faker) {

    return [
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'date' => $faker->date('Y-m-d H:i:s'),
        'slot_master_id' => $faker->randomDigitNotNull,
        'status' => $faker->randomDigitNotNull,
        'time_from' => $faker->randomDigitNotNull,
        'time_to' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
