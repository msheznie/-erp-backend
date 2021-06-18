<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomerMasterCategoryAssigned;
use Faker\Generator as Faker;

$factory->define(CustomerMasterCategoryAssigned::class, function (Faker $faker) {

    return [
        'customerMasterCategoryID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'categoryDescription' => $faker->word,
        'createdUserID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'isAssigned' => $faker->word,
        'isActive' => $faker->word
    ];
});
