<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SrmDepartmentMaster;
use Faker\Generator as Faker;

$factory->define(SrmDepartmentMaster::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'is_active' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull
    ];
});
