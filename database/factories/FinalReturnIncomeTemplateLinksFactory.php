<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FinalReturnIncomeTemplateLinks;
use Faker\Generator as Faker;

$factory->define(FinalReturnIncomeTemplateLinks::class, function (Faker $faker) {

    return [
        'templateMasterID' => $faker->word,
        'templateDetailID' => $faker->word,
        'sortOrder' => $faker->randomDigitNotNull,
        'glAutoID' => $faker->word,
        'glCode' => $faker->word,
        'glDescription' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserSystemID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserSystemID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
