<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMEPayAsset;
use Faker\Generator as Faker;

$factory->define(SMEPayAsset::class, function (Faker $faker) {

    return [
        'empID' => $faker->randomDigitNotNull,
        'assetTypeID' => $faker->randomDigitNotNull,
        'description' => $faker->text,
        'asset_serial_no' => $faker->word,
        'assetConditionID' => $faker->randomDigitNotNull,
        'handOverDate' => $faker->word,
        'returnStatus' => $faker->randomDigitNotNull,
        'returnDate' => $faker->word,
        'returnComment' => $faker->text,
        'companyID' => $faker->randomDigitNotNull,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->randomDigitNotNull,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
