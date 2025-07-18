<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderBudgetItemEditLog;
use Faker\Generator as Faker;

$factory->define(TenderBudgetItemEditLog::class, function (Faker $faker) {

    return [
        'budget_amount' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'id' => $faker->randomDigitNotNull,
        'item_id' => $faker->randomDigitNotNull,
        'level_no' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'version_id' => $faker->randomDigitNotNull
    ];
});
