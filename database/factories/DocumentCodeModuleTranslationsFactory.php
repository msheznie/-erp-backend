<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentCodeModuleTranslations;
use Faker\Generator as Faker;

$factory->define(DocumentCodeModuleTranslations::class, function (Faker $faker) {

    return [
        'documentId' => $faker->word,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
