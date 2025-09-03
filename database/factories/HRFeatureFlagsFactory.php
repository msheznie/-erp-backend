<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HRFeatureFlags;
use Faker\Generator as Faker;

$factory->define(HRFeatureFlags::class, function (Faker $faker) {

    return [
        'feature_name' => $faker->word,
        'is_enabled' => $faker->word,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
