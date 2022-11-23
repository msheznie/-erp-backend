<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSTransErrorLog;
use Faker\Generator as Faker;

$factory->define(POSTransErrorLog::class, function (Faker $faker) {

    return [
        'log_id' => $faker->randomDigitNotNull,
        'error' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
