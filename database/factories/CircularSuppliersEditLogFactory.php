<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CircularSuppliersEditLog;
use Faker\Generator as Faker;

$factory->define(CircularSuppliersEditLog::class, function (Faker $faker) {

    return [
        'circular_id' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'id' => $faker->randomDigitNotNull,
        'is_deleted' => $faker->randomDigitNotNull,
        'level_no' => $faker->randomDigitNotNull,
        'status' => $faker->randomDigitNotNull,
        'supplier_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'version_id' => $faker->randomDigitNotNull
    ];
});
