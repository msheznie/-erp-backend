<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomUserReports;
use Faker\Generator as Faker;

$factory->define(CustomUserReports::class, function (Faker $faker) {

    return [
        'user_id' => $faker->randomDigitNotNull,
        'report_master_id' => $faker->randomDigitNotNull,
        'name' => $faker->word,
        'is_private' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
