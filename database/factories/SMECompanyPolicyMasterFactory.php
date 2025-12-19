<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMECompanyPolicyMaster;
use Faker\Generator as Faker;

$factory->define(SMECompanyPolicyMaster::class, function (Faker $faker) {

    return [
        'companyPolicyDescription' => $faker->word,
        'systemValue' => $faker->word,
        'isDocumentLevel' => $faker->randomDigitNotNull,
        'code' => $faker->word,
        'documentID' => $faker->word,
        'defaultValue' => $faker->word,
        'fieldType' => $faker->word,
        'is_active' => $faker->randomDigitNotNull,
        'isCompanyLevel' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
