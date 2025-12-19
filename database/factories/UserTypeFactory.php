<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserType;
use Faker\Generator as Faker;

$factory->define(UserType::class, function (Faker $faker) {

    return [
        'userType' => $faker->word,
        'slug' => $faker->word,
        'isSystemUser' => $faker->word,
        'isProductSuperAdmin' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
