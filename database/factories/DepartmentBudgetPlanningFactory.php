<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DepartmentBudgetPlanning;
use Faker\Generator as Faker;

$factory->define(DepartmentBudgetPlanning::class, function (Faker $faker) {

    return [
        'companyBudgetPlanningID' => $faker->randomDigitNotNull,
        'departmentID' => $faker->randomDigitNotNull,
        'initiatedDate' => $faker->word,
        'periodID' => $faker->randomDigitNotNull,
        'yearID' => $faker->randomDigitNotNull,
        'typeID' => $faker->randomDigitNotNull,
        'submissionDate' => $faker->word,
        'workflowID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
