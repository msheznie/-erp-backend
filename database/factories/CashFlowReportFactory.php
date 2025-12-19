<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CashFlowReport;
use Faker\Generator as Faker;

$factory->define(CashFlowReport::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'cashFlowTemplateID' => $faker->randomDigitNotNull,
        'companyFinanceYearID' => $faker->randomDigitNotNull,
        'date' => $faker->word,
        'createdPCID' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'modifiedPCID' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'confirmed_by' => $faker->randomDigitNotNull,
        'confirmed_date' => $faker->word,
        'confirmedYN' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
