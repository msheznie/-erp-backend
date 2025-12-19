<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EvaluationCriteriaDetailsEditLog;
use Faker\Generator as Faker;

$factory->define(EvaluationCriteriaDetailsEditLog::class, function (Faker $faker) {

    return [
        'answer_type_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'critera_type_id' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'is_final_level' => $faker->randomDigitNotNull,
        'level' => $faker->randomDigitNotNull,
        'master_id' => $faker->randomDigitNotNull,
        'max_value' => $faker->randomDigitNotNull,
        'min_value' => $faker->randomDigitNotNull,
        'modify_type' => $faker->randomDigitNotNull,
        'parent_id' => $faker->randomDigitNotNull,
        'passing_weightage' => $faker->randomDigitNotNull,
        'ref_log_id' => $faker->randomDigitNotNull,
        'sort_order' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'weightage' => $faker->randomDigitNotNull
    ];
});
