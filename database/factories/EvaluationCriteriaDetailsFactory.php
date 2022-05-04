<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EvaluationCriteriaDetails;
use Faker\Generator as Faker;

$factory->define(EvaluationCriteriaDetails::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'parent_id' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'critera_type_id' => $faker->randomDigitNotNull,
        'answer_type_id' => $faker->randomDigitNotNull,
        'level' => $faker->randomDigitNotNull,
        'is_final_level' => $faker->randomDigitNotNull,
        'weightage' => $faker->randomDigitNotNull,
        'passing_weightage' => $faker->randomDigitNotNull,
        'sort_order' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
