<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ScheduleBidFormatDetails;
use Faker\Generator as Faker;

$factory->define(ScheduleBidFormatDetails::class, function (Faker $faker) {

    return [
        'bid_format_detail_id' => $faker->randomDigitNotNull,
        'schedule_id' => $faker->randomDigitNotNull,
        'value' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull
    ];
});
