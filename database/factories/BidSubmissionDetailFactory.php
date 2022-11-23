<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BidSubmissionDetail;
use Faker\Generator as Faker;

$factory->define(BidSubmissionDetail::class, function (Faker $faker) {

    return [
        'bid_master_id' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'evaluation_detail_id' => $faker->randomDigitNotNull,
        'score_id' => $faker->randomDigitNotNull,
        'score' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
