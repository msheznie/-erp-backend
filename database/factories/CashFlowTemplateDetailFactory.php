<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CashFlowTemplateDetail;
use Faker\Generator as Faker;

$factory->define(CashFlowTemplateDetail::class, function (Faker $faker) {

    return [
        'cashFlowTemplateID' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'type' => $faker->randomDigitNotNull,
        'masterID' => $faker->randomDigitNotNull,
        'sortOrder' => $faker->randomDigitNotNull,
        'subExits' => $faker->randomDigitNotNull,
        'logicType' => $faker->randomDigitNotNull,
        'controlAccountType' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'modifiedPCID' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
