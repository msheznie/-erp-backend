<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\WorkflowConfiguration;
use Faker\Generator as Faker;

$factory->define(WorkflowConfiguration::class, function (Faker $faker) {

    return [
        'workflowName' => $faker->word,
        'initiateBudget' => $faker->randomDigitNotNull,
        'method' => $faker->randomDigitNotNull,
        'allocation' => $faker->randomDigitNotNull,
        'finalApproval' => $faker->randomDigitNotNull,
        'isActive' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
