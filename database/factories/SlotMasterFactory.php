<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SlotMaster;
use Faker\Generator as Faker;

$factory->define(SlotMaster::class, function (Faker $faker) {

    return [
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'from_date' => $faker->date('Y-m-d H:i:s'),
        'no_of_deliveries' => $faker->randomDigitNotNull,
        'time_from' => $faker->randomDigitNotNull,
        'time_to' => $faker->randomDigitNotNull,
        'to_date' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'warehouse_id' => $faker->randomDigitNotNull
    ];
});
