<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMECompanyPolicyValue;
use Faker\Generator as Faker;

$factory->define(SMECompanyPolicyValue::class, function (Faker $faker) {

    return [
        'companypolicymasterID' => $faker->randomDigitNotNull,
        'value' => $faker->word,
        'systemValue' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
