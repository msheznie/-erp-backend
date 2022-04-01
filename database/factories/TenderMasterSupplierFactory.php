<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderMasterSupplier;
use Faker\Generator as Faker;

$factory->define(TenderMasterSupplier::class, function (Faker $faker) {

    return [
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'purchased_by' => $faker->randomDigitNotNull,
        'purchased_date' => $faker->date('Y-m-d H:i:s'),
        'status' => $faker->randomDigitNotNull,
        'tender_master_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
