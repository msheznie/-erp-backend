<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSTransLog;
use Faker\Generator as Faker;

$factory->define(POSTransLog::class, function (Faker $faker) {

    return [
        'pos_mapping_id' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'created_date' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'updated_date' => $faker->date('Y-m-d H:i:s'),
        'status' => $faker->randomDigitNotNull
    ];
});
