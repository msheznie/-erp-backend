<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EvaluationCriteriaScoreConfig;
use Faker\Generator as Faker;

$factory->define(EvaluationCriteriaScoreConfig::class, function (Faker $faker) {

    return [
        'criteria_detail_id' => $faker->randomDigitNotNull,
        'label' => $faker->word,
        'score' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
