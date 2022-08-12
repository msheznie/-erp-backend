<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSStagPaymentGlConfig;
use Faker\Generator as Faker;

$factory->define(POSStagPaymentGlConfig::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'glAccountType' => $faker->randomDigitNotNull,
        'queryString' => $faker->text,
        'image' => $faker->word,
        'isActive' => $faker->randomDigitNotNull,
        'sortOrder' => $faker->randomDigitNotNull,
        'selectBoxName' => $faker->word,
        'timesstamp' => $faker->date('Y-m-d H:i:s'),
        'transaction_log_id' => $faker->randomDigitNotNull
    ];
});
