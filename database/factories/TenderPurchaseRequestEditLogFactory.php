<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderPurchaseRequestEditLog;
use Faker\Generator as Faker;

$factory->define(TenderPurchaseRequestEditLog::class, function (Faker $faker) {

    return [
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'id' => $faker->randomDigitNotNull,
        'level_no' => $faker->randomDigitNotNull,
        'purchase_request_id' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'version_id' => $faker->randomDigitNotNull
    ];
});
