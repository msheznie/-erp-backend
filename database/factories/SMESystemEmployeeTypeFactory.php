<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMESystemEmployeeType;
use Faker\Generator as Faker;

$factory->define(SMESystemEmployeeType::class, function (Faker $faker) {

    return [
        'employeeType' => $faker->word
    ];
});
