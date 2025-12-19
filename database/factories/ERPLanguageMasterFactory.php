<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ERPLanguageMaster;
use Faker\Generator as Faker;

$factory->define(ERPLanguageMaster::class, function (Faker $faker) {

    return [
        'systemDescription' => $faker->word,
        'description' => $faker->word,
        'languageShortCode' => $faker->word,
        'languageSecShortCode' => $faker->word,
        'isActive' => $faker->randomDigitNotNull,
        'icon' => $faker->word
    ];
});
