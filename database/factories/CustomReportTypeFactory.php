<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomReportType;
use Faker\Generator as Faker;

$factory->define(CustomReportType::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'is_active' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
