<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ChequeTemplateMaster;
use Faker\Generator as Faker;

$factory->define(ChequeTemplateMaster::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'view_name' => $faker->word,
        'is_active' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
