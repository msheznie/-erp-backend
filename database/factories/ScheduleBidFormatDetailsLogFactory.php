<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ScheduleBidFormatDetailsLog;
use Faker\Generator as Faker;

$factory->define(ScheduleBidFormatDetailsLog::class, function (Faker $faker) {

    return [
        'bid_format_detail_id' => $faker->randomDigitNotNull,
        'bid_master_id' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'master_id' => $faker->randomDigitNotNull,
        'modify_type' => $faker->randomDigitNotNull,
        'red_log_id' => $faker->randomDigitNotNull,
        'schedule_id' => $faker->randomDigitNotNull,
        'tender_edit_version_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'value' => $faker->word
    ];
});
