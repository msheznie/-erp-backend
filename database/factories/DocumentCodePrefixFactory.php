<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentCodePrefix;
use Faker\Generator as Faker;

$factory->define(DocumentCodePrefix::class, function (Faker $faker) {

    return [
        'type_based_id' => $faker->randomDigitNotNull,
        'common_id' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'format' => $faker->word,
        'company_id' => $faker->randomDigitNotNull
    ];
});
