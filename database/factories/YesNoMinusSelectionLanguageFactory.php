<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\YesNoMinusSelectionLanguage;
use Faker\Generator as Faker;

$factory->define(YesNoMinusSelectionLanguage::class, function (Faker $faker) {

    return [
        'yesNoSelectionID' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'YesNo' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
