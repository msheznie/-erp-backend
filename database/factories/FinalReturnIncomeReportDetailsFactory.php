<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FinalReturnIncomeReportDetails;
use Faker\Generator as Faker;

$factory->define(FinalReturnIncomeReportDetails::class, function (Faker $faker) {

    return [
        'report_id' => $faker->word,
        'template_detail_id' => $faker->word,
        'amount' => $faker->randomDigitNotNull,
        'is_manual' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
