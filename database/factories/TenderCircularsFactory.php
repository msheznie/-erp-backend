<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderCirculars;
use Faker\Generator as Faker;

$factory->define(TenderCirculars::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'circular_name' => $faker->word,
        'description' => $faker->word,
        'attachment_id' => $faker->randomDigitNotNull,
        'status' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'deleted_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_by' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull
    ];
});
