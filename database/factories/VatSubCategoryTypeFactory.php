<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VatSubCategoryType;
use Faker\Generator as Faker;

$factory->define(VatSubCategoryType::class, function (Faker $faker) {

    return [
        'type' => $faker->word
    ];
});
