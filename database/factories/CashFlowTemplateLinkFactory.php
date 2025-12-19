<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CashFlowTemplateLink;
use Faker\Generator as Faker;

$factory->define(CashFlowTemplateLink::class, function (Faker $faker) {

    return [
        'templateMasterID' => $faker->randomDigitNotNull,
        'templateDetailID' => $faker->randomDigitNotNull,
        'sortOrder' => $faker->randomDigitNotNull,
        'glAutoID' => $faker->randomDigitNotNull,
        'glCode' => $faker->word,
        'glDescription' => $faker->word,
        'subCategory' => $faker->randomDigitNotNull,
        'categoryType' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'modifiedPCID' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
