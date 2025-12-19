<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomUserReportSummarize;
use Faker\Generator as Faker;

$factory->define(CustomUserReportSummarize::class, function (Faker $faker) {

    return [
        'user_report_id' => $faker->randomDigitNotNull,
        'column_id' => $faker->randomDigitNotNull,
        'type_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
