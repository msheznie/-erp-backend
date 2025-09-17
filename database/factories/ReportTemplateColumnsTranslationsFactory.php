<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ReportTemplateColumnsTranslations;
use Faker\Generator as Faker;

$factory->define(ReportTemplateColumnsTranslations::class, function (Faker $faker) {

    return [
        'columnID' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
