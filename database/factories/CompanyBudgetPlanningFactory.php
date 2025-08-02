<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CompanyBudgetPlanning;
use Faker\Generator as Faker;

$factory->define(CompanyBudgetPlanning::class, function (Faker $faker) {

    return [
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
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
