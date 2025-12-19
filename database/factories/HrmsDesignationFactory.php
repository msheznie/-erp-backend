<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HrmsDesignation;
use Faker\Generator as Faker;

$factory->define(HrmsDesignation::class, function (Faker $faker) {

    return [
        'DesDescription' => $faker->word,
        'isRequiredSelection' => $faker->randomDigitNotNull,
        'SelectionID' => $faker->randomDigitNotNull,
        'DesDashboardID' => $faker->randomDigitNotNull,
        'SchMasterID' => $faker->randomDigitNotNull,
        'BranchID' => $faker->randomDigitNotNull,
        'Erp_companyID' => $faker->randomDigitNotNull,
        'isDeleted' => $faker->randomDigitNotNull,
        'CreatedUserName' => $faker->word,
        'CreatedDate' => $faker->date('Y-m-d H:i:s'),
        'CreatedPC' => $faker->word,
        'ModifiedUserName' => $faker->word,
        'Timestamp' => $faker->date('Y-m-d H:i:s'),
        'ModifiedPC' => $faker->word,
        'SortOrder' => $faker->randomDigitNotNull
    ];
});
