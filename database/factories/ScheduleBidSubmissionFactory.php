<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ScheduleBidSubmission;
use Faker\Generator as Faker;

$factory->define(ScheduleBidSubmission::class, function (Faker $faker) {

    return [
        'schedule_id' => $faker->randomDigitNotNull,
        'bid_master_id' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'supplier_registration_id' => $faker->randomDigitNotNull,
        'remarks' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
