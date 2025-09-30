<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentEmailNotificationMasterTranslations;
use Faker\Generator as Faker;

$factory->define(DocumentEmailNotificationMasterTranslations::class, function (Faker $faker) {

    return [
        'emailNotificationID' => $faker->word,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
