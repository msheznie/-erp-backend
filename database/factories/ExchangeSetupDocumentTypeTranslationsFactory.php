<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ExchangeSetupDocumentTypeTranslations;
use Faker\Generator as Faker;

$factory->define(ExchangeSetupDocumentTypeTranslations::class, function (Faker $faker) {

    return [
        'slug' => $faker->word,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
