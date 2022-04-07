<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderMainWorks;
use Faker\Generator as Faker;

$factory->define(TenderMainWorks::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'schedule_id' => $faker->randomDigitNotNull,
        'item' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull
    ];
});
