<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HodAction;
use Faker\Generator as Faker;

$factory->define(HodAction::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
