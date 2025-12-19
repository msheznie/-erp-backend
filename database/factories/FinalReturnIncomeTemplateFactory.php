<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FinalReturnIncomeTemplate;
use Faker\Generator as Faker;

$factory->define(FinalReturnIncomeTemplate::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'description' => $faker->word,
        'isActive' => $faker->word,
        'isDefault' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'modifiedPCID' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
