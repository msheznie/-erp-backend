<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PoCategory;
use Faker\Generator as Faker;

$factory->define(PoCategory::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'isActive' => $faker->word,
        'isDefault' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s')
    ];
});
