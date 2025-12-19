<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SubModuleMaster;
use Faker\Generator as Faker;

$factory->define(SubModuleMaster::class, function (Faker $faker) {

    return [
        'subModuleName' => $faker->word,
        'moduleMasterID' => $faker->randomDigitNotNull
    ];
});
