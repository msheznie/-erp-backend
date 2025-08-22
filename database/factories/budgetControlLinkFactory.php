<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\budgetControlLink;
use Faker\Generator as Faker;

$factory->define(budgetControlLink::class, function (Faker $faker) {

    return [
        'companySystemID' => $faker->randomDigitNotNull,
        'controlId' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'createdPCID' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'glAutoID' => $faker->randomDigitNotNull,
        'glCode' => $faker->word,
        'glDescription' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'sortOrder' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
