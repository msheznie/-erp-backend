<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ExpenseClaimCategoriesMaster;
use Faker\Generator as Faker;

$factory->define(ExpenseClaimCategoriesMaster::class, function (Faker $faker) {

    return [
        'claimcategoriesDescription' => $faker->text,
        'glAutoID' => $faker->randomDigitNotNull,
        'glCode' => $faker->word,
        'glCodeDescription' => $faker->text,
        'type' => $faker->randomDigitNotNull,
        'fuelUsageYN' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
