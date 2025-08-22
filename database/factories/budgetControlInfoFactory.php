<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\budgetControlInfo;
use Faker\Generator as Faker;

$factory->define(budgetControlInfo::class, function (Faker $faker) {

    return [
        'companySystemID' => $faker->randomDigitNotNull,
        'controlName' => $faker->word,
        'controlType' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'createdPCID' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'definedBehavior' => $faker->randomDigitNotNull,
        'ignoreBudget' => $faker->word,
        'ignoreGl' => $faker->word,
        'isChecked' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
