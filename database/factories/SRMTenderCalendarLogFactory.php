<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SRMTenderCalendarLog;
use Faker\Generator as Faker;

$factory->define(SRMTenderCalendarLog::class, function (Faker $faker) {

    return [
        'filed_description' => $faker->word,
        'old_value' => $faker->word,
        'new_value' => $faker->word,
        'tender_id' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
