<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PricingScheduleMasterEditLog;
use Faker\Generator as Faker;

$factory->define(PricingScheduleMasterEditLog::class, function (Faker $faker) {

    return [
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'items_mandatory' => $faker->randomDigitNotNull,
        'modify_type' => $faker->randomDigitNotNull,
        'price_bid_format_id' => $faker->randomDigitNotNull,
        'schedule_mandatory' => $faker->randomDigitNotNull,
        'scheduler_name' => $faker->word,
        'status' => $faker->randomDigitNotNull,
        'tender_edit_version_id' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
