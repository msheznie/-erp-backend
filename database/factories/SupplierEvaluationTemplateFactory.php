<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SupplierEvaluationTemplate;
use Faker\Generator as Faker;

$factory->define(SupplierEvaluationTemplate::class, function (Faker $faker) {

    return [
        'template_name' => $faker->word,
        'template_type' => $faker->randomDigitNotNull,
        'is_active' => $faker->randomDigitNotNull,
        'is_confirmed' => $faker->randomDigitNotNull,
        'is_draft' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
