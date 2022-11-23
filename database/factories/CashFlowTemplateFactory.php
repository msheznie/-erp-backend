<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CashFlowTemplate;
use Faker\Generator as Faker;

$factory->define(CashFlowTemplate::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'type' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'isActive' => $faker->randomDigitNotNull,
        'presentationType' => $faker->randomDigitNotNull,
        'showNumbersIn' => $faker->randomDigitNotNull,
        'showDecimalPlaceYN' => $faker->randomDigitNotNull,
        'showZeroGlYN' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'modifiedPCID' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
