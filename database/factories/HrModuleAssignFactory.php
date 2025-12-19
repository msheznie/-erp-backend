<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HrModuleAssign;
use Faker\Generator as Faker;

$factory->define(HrModuleAssign::class, function (Faker $faker) {

    return [
        'module_id' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'assign_date' => $faker->word
    ];
});
