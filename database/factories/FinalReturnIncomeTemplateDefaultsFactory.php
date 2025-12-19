<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FinalReturnIncomeTemplateDefaults;
use Faker\Generator as Faker;

$factory->define(FinalReturnIncomeTemplateDefaults::class, function (Faker $faker) {

    return [
        'line_no' => $faker->randomDigitNotNull,
        'type' => $faker->word,
        'description' => $faker->word,
        'appendix' => $faker->word,
        'sectionType' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
