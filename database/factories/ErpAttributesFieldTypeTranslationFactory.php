<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ErpAttributesFieldTypeTranslation;
use Faker\Generator as Faker;

$factory->define(ErpAttributesFieldTypeTranslation::class, function (Faker $faker) {

    return [
        'fieldTypeId' => $faker->randomDigitNotNull,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
