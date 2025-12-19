<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomFiltersColumn;
use Faker\Generator as Faker;

$factory->define(CustomFiltersColumn::class, function (Faker $faker) {

    return [
        'column_id' => $faker->randomDigitNotNull,
        'operator' => $faker->word,
        'value' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
