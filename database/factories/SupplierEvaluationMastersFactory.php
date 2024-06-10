<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SupplierEvaluationMasters;
use Faker\Generator as Faker;

$factory->define(SupplierEvaluationMasters::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'type' => $faker->randomDigitNotNull,
        'is_active' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
