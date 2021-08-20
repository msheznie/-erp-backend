<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BudgetDetailComment;
use Faker\Generator as Faker;

$factory->define(BudgetDetailComment::class, function (Faker $faker) {

    return [
        'budgetDetailID' => $faker->randomDigitNotNull,
        'comment' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull
    ];
});
