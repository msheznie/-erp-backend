<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ModuleMaster;
use Faker\Generator as Faker;

$factory->define(ModuleMaster::class, function (Faker $faker) {

    return [
        'moduleName' => $faker->word
    ];
});
