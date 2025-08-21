<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FinalReturnIncomeTemplateDetails;
use Faker\Generator as Faker;

$factory->define(FinalReturnIncomeTemplateDetails::class, function (Faker $faker) {

    return [
        'templateMasterID' => $faker->word,
        'description' => $faker->word,
        'itemType' => $faker->randomDigitNotNull,
        'sectionType' => $faker->randomDigitNotNull,
        'sortOrder' => $faker->randomDigitNotNull,
        'masterID' => $faker->word,
        'isFinalLevel' => $faker->word,
        'bgColor' => $faker->word,
        'fontColor' => $faker->word,
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
