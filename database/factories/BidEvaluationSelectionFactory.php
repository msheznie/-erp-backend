<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BidEvaluationSelection;
use Faker\Generator as Faker;

$factory->define(BidEvaluationSelection::class, function (Faker $faker) {

    return [
        'bids' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'status' => $faker->word,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
