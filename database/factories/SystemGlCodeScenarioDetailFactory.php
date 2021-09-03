<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SystemGlCodeScenarioDetail;
use Faker\Generator as Faker;

$factory->define(SystemGlCodeScenarioDetail::class, function (Faker $faker) {

    return [
        'systemGlScenarioID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'chartOfAccountSystemID' => $faker->randomDigitNotNull,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
