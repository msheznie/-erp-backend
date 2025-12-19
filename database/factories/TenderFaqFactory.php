<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderFaq;
use Faker\Generator as Faker;

$factory->define(TenderFaq::class, function (Faker $faker) {

    return [
        'answer' => $faker->text,
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'question' => $faker->word,
        'tender_master_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
