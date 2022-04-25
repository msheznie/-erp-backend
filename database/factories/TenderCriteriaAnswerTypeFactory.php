<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderCriteriaAnswerType;
use Faker\Generator as Faker;

$factory->define(TenderCriteriaAnswerType::class, function (Faker $faker) {

    return [
        'type' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s')
    ];
});
