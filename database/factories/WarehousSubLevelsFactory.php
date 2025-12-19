<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\WarehousSubLevels;
use Faker\Generator as Faker;

$factory->define(WarehousSubLevels::class, function (Faker $faker) {

    return [
        'company_id' => $faker->randomDigitNotNull,
        'warehouse_id' => $faker->randomDigitNotNull,
        'level' => $faker->randomDigitNotNull,
        'parent_id' => $faker->randomDigitNotNull,
        'name' => $faker->word,
        'description' => $faker->word,
        'isFinalLevel' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_pc' => $faker->word,
        'updated_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_pc' => $faker->word
    ];
});
