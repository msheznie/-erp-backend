<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ThirdPartyDomain;
use Faker\Generator as Faker;

$factory->define(ThirdPartyDomain::class, function (Faker $faker) {

    return [
        'thirdPartySystemId' => $faker->randomDigitNotNull,
        'name' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
