<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ErpAttributesDropdown;
use Faker\Generator as Faker;

$factory->define(ErpAttributesDropdown::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'attributes_id' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
