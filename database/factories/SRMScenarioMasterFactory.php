<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SRMScenarioMaster;
use Faker\Generator as Faker;

$factory->define(SRMScenarioMaster::class, function (Faker $faker) {

    return [
        'document_id' => $faker->randomDigitNotNull,
        'email_scenario_code' => $faker->word,
        'email_scenario_name' => $faker->word,
        'company_id' => $faker->randomDigitNotNull,
        'is_active' => $faker->word
    ];
});
