<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FinalReturnIncomeReportDetailValues;
use Faker\Generator as Faker;

$factory->define(FinalReturnIncomeReportDetailValues::class, function (Faker $faker) {

    return [
        'report_detail_id' => $faker->word,
        'column_id' => $faker->word,
        'amount' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
