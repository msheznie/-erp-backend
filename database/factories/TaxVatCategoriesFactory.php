<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TaxVatCategories;
use Faker\Generator as Faker;

$factory->define(TaxVatCategories::class, function (Faker $faker) {

    return [
        'taxMasterAutoID' => $faker->randomDigitNotNull,
        'mainCategory' => $faker->randomDigitNotNull,
        'subCategory' => $faker->randomDigitNotNull,
        'percentage' => $faker->randomDigitNotNull,
        'applicableOn' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
