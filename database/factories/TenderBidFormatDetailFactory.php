<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderBidFormatDetail;
use Faker\Generator as Faker;

$factory->define(TenderBidFormatDetail::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'label' => $faker->word,
        'field_type' => $faker->randomDigitNotNull,
        'is_disabled' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
