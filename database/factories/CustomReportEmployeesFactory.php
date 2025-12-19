<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomReportEmployees;
use Faker\Generator as Faker;

$factory->define(CustomReportEmployees::class, function (Faker $faker) {

    return [
        'user_id' => $faker->randomDigitNotNull,
        'report_master_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
