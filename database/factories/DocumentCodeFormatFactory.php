<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentCodeFormat;
use Faker\Generator as Faker;

$factory->define(DocumentCodeFormat::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'column_name' => $faker->word,
        'is_active' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
