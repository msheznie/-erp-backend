<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DepartmentMasterTranslations;
use Faker\Generator as Faker;

$factory->define(DepartmentMasterTranslations::class, function (Faker $faker) {

    return [
        'DepartmentID' => $faker->word,
        'languageCode' => $faker->word,
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
