<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SupplierEvaluationTemplateSectionTable;
use Faker\Generator as Faker;

$factory->define(SupplierEvaluationTemplateSectionTable::class, function (Faker $faker) {

    return [
        'supplier_evaluation_template_id' => $faker->randomDigitNotNull,
        'table_name' => $faker->word,
        'table_row' => $faker->randomDigitNotNull,
        'table_column' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
