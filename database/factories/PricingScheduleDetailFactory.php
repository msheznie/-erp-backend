<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PricingScheduleDetail;
use Faker\Generator as Faker;

$factory->define(PricingScheduleDetail::class, function (Faker $faker) {

    return [
        'bid_format_id' => $faker->randomDigitNotNull,
        'boq_applicable' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'deleted_at' => $faker->date('Y-m-d H:i:s'),
        'field_type' => $faker->randomDigitNotNull,
        'formula_string' => $faker->text,
        'is_disabled' => $faker->randomDigitNotNull,
        'label' => $faker->word,
        'pricing_schedule_master_id' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
