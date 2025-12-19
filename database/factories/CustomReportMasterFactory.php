<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomReportMaster;
use Faker\Generator as Faker;

$factory->define(CustomReportMaster::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'report_type_id' => $faker->randomDigitNotNull,
        'is_active' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
