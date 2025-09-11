<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ItemCategoryTypeMasterTranslation;
use Faker\Generator as Faker;

$factory->define(ItemCategoryTypeMasterTranslation::class, function (Faker $faker) {

    return [
        'typeId' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'name' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
