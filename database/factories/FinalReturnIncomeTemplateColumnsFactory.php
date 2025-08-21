<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FinalReturnIncomeTemplateColumns;
use Faker\Generator as Faker;

$factory->define(FinalReturnIncomeTemplateColumns::class, function (Faker $faker) {

    return [
        'templateMasterID' => $faker->word,
        'description' => $faker->word,
        'sortOrder' => $faker->randomDigitNotNull,
        'isHide' => $faker->word,
        'width' => $faker->randomDigitNotNull,
        'bgColor' => $faker->word,
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
