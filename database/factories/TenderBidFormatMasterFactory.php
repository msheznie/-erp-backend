<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderBidFormatMaster;
use Faker\Generator as Faker;

$factory->define(TenderBidFormatMaster::class, function (Faker $faker) {

    return [
        'tender_name' => $faker->word,
        'boq_applicable' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'deleted_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_by' => $faker->randomDigitNotNull
    ];
});
