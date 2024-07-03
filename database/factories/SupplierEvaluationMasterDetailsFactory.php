<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SupplierEvaluationMasterDetails;
use Faker\Generator as Faker;

$factory->define(SupplierEvaluationMasterDetails::class, function (Faker $faker) {

    return [
        'master_id' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'score' => $faker->randomDigitNotNull,
        'rating' => $faker->word,
        'comment' => $faker->word,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
