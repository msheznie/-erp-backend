<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AssetTypeTranslation;
use Faker\Generator as Faker;

$factory->define(AssetTypeTranslation::class, function (Faker $faker) {

    return [
        'typeID' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'data' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
