<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EvaluationTemplateSection;
use Faker\Generator as Faker;

$factory->define(EvaluationTemplateSection::class, function (Faker $faker) {

    return [
        'supplier_evaluation_template_id' => $faker->randomDigitNotNull,
        'section_name' => $faker->word,
        'section_type' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
