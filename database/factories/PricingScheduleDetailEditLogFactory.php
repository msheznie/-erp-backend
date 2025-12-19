<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PricingScheduleDetailEditLog;
use Faker\Generator as Faker;

$factory->define(PricingScheduleDetailEditLog::class, function (Faker $faker) {

    return [
        'bid_format_detail_id' => $faker->randomDigitNotNull,
        'bid_format_id' => $faker->randomDigitNotNull,
        'boq_applicable' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'deleted_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_by' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'field_type' => $faker->randomDigitNotNull,
        'formula_string' => $faker->text,
        'is_disabled' => $faker->randomDigitNotNull,
        'label' => $faker->word,
        'modify_type' => $faker->randomDigitNotNull,
        'pricing_schedule_master_id' => $faker->randomDigitNotNull,
        'tender_edit_version_id' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'tender_ranking_line_item' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
