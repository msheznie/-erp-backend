<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BidMainWork;
use Faker\Generator as Faker;

$factory->define(BidMainWork::class, function (Faker $faker) {

    return [
        'main_works_id' => $faker->randomDigitNotNull,
        'bid_master_id' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'bid_format_detail_id' => $faker->randomDigitNotNull,
        'qty' => $faker->randomDigitNotNull,
        'amount' => $faker->randomDigitNotNull,
        'remarks' => $faker->word,
        'supplier_registration_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
