<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderSiteVisitDates;
use Faker\Generator as Faker;

$factory->define(TenderSiteVisitDates::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'date' => $faker->date('Y-m-d H:i:s'),
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull
    ];
});
