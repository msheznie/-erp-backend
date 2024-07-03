<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SupplierEvaluationTemplateComment;
use Faker\Generator as Faker;

$factory->define(SupplierEvaluationTemplateComment::class, function (Faker $faker) {

    return [
        'supplier_evaluation_template_id' => $faker->randomDigitNotNull,
        'label' => $faker->word,
        'comment' => $faker->word,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
