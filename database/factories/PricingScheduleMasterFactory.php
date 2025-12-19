<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PricingScheduleMaster;
use Faker\Generator as Faker;

$factory->define(PricingScheduleMaster::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'scheduler_name' => $faker->word,
        'price_bid_format_id' => $faker->randomDigitNotNull,
        'schedule_mandatory' => $faker->randomDigitNotNull,
        'items_mandatory' => $faker->randomDigitNotNull,
        'status' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull
    ];
});
