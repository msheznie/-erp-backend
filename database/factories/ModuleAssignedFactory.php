<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ModuleAssigned;
use Faker\Generator as Faker;

$factory->define(ModuleAssigned::class, function (Faker $faker) {

    return [
        'companySystemID' => $faker->randomDigitNotNull,
        'moduleID' => $faker->randomDigitNotNull,
        'subModuleID' => $faker->randomDigitNotNull
    ];
});
