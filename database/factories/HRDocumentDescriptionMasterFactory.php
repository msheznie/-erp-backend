<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HRDocumentDescriptionMaster;
use Faker\Generator as Faker;

$factory->define(HRDocumentDescriptionMaster::class, function (Faker $faker) {

    return [
        'DocDescription' => $faker->word,
        'systemTypeID' => $faker->randomDigitNotNull,
        'SchMasterID' => $faker->randomDigitNotNull,
        'BranchID' => $faker->randomDigitNotNull,
        'Erp_companyID' => $faker->randomDigitNotNull,
        'isDeleted' => $faker->randomDigitNotNull,
        'CreatedUserName' => $faker->word,
        'createdUserID' => $faker->randomDigitNotNull,
        'CreatedDate' => $faker->date('Y-m-d H:i:s'),
        'CreatedPC' => $faker->word,
        'modifiedUserID' => $faker->randomDigitNotNull,
        'ModifiedUserName' => $faker->word,
        'Timestamp' => $faker->date('Y-m-d H:i:s'),
        'ModifiedPC' => $faker->word,
        'SortOrder' => $faker->randomDigitNotNull
    ];
});
