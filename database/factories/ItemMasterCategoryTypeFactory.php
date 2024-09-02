<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ItemMasterCategoryType;
use Faker\Generator as Faker;

$factory->define(ItemMasterCategoryType::class, function (Faker $faker) {

    return [
        'itemCodeSystem' => $faker->randomDigitNotNull,
        'categoryTypeID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
