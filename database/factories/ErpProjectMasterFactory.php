<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ErpProjectMaster;
use Faker\Generator as Faker;

$factory->define(ErpProjectMaster::class, function (Faker $faker) {

    return [
        'projectCode' => $faker->word,
        'description' => $faker->word,
        'companyID' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'serviceLineCode' => $faker->word,
        'projectCurrencyID' => $faker->randomDigitNotNull,
        'companyLocalCurrencyID' => $faker->randomDigitNotNull,
        'companyRptCurrencyID' => $faker->randomDigitNotNull,
        'estimatedAmount' => $faker->randomDigitNotNull,
        'estimatedLocalAmount' => $faker->randomDigitNotNull,
        'estimatedRptAmount' => $faker->randomDigitNotNull,
        'start_date' => $faker->word,
        'end_date' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->randomDigitNotNull,
        'modifiedUserID' => $faker->randomDigitNotNull,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
