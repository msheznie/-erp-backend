<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RoleRoute;
use Faker\Generator as Faker;

$factory->define(RoleRoute::class, function (Faker $faker) {

    return [
        'routeName' => $faker->word,
        'userGroupID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
