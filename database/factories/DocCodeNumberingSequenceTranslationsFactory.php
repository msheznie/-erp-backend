<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocCodeNumberingSequenceTranslations;
use Faker\Generator as Faker;

$factory->define(DocCodeNumberingSequenceTranslations::class, function (Faker $faker) {

    return [
        'sequenceId' => $faker->word,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
