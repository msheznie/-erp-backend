<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CommercialBidRankingItems;
use Faker\Generator as Faker;

$factory->define(CommercialBidRankingItems::class, function (Faker $faker) {

    return [
        'bid_format_detail_id' => $faker->randomDigitNotNull,
        'bid_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'status' => $faker->word,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'value' => $faker->randomDigitNotNull
    ];
});
