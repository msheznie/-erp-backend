<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SupplierEvaluationTemplateSectionTableColumn;
use Faker\Generator as Faker;

$factory->define(SupplierEvaluationTemplateSectionTableColumn::class, function (Faker $faker) {

    return [
        'table_id' => $faker->randomDigitNotNull,
        'column_header' => $faker->word,
        'column_type' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
