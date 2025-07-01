<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderSiteVisitDateEditLog;
use Faker\Generator as Faker;

$factory->define(TenderSiteVisitDateEditLog::class, function (Faker $faker) {

    return [
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'date' => $faker->date('Y-m-d H:i:s'),
        'id' => $faker->randomDigitNotNull,
        'is_deleted' => $faker->randomDigitNotNull,
        'level_no' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'version_id' => $faker->randomDigitNotNull
    ];
});
