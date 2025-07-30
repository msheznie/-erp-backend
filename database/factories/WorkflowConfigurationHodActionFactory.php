<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\WorkflowConfigurationHodAction;
use Faker\Generator as Faker;

$factory->define(WorkflowConfigurationHodAction::class, function (Faker $faker) {

    return [
        'workflowConfigurationID' => $faker->randomDigitNotNull,
        'hodActionID' => $faker->randomDigitNotNull,
        'parent' => $faker->randomDigitNotNull,
        'child' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
