<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EvaluationCriteriaType;
use Faker\Generator as Faker;

$factory->define(EvaluationCriteriaType::class, function (Faker $faker) {

    return [
        'criteria' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s')
    ];
});
