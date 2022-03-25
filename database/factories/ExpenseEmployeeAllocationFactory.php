<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ExpenseEmployeeAllocation;
use Faker\Generator as Faker;

$factory->define(ExpenseEmployeeAllocation::class, function (Faker $faker) {

    return [
        'employeeSystemID' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentDetailID' => $faker->randomDigitNotNull,
        'chartOfAccountSystemID' => $faker->randomDigitNotNull,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'amount' => $faker->randomDigitNotNull,
        'amountRpt' => $faker->randomDigitNotNull,
        'amountLocal' => $faker->randomDigitNotNull,
        'dateOfDeduction' => $faker->date('Y-m-d H:i:s'),
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
