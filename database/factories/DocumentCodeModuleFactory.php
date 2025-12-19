<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentCodeModule;
use Faker\Generator as Faker;

$factory->define(DocumentCodeModule::class, function (Faker $faker) {

    return [
        'module_name' => $faker->word,
        'is_active' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
