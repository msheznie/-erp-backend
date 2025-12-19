<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMECompanyPolicy;
use Faker\Generator as Faker;

$factory->define(SMECompanyPolicy::class, function (Faker $faker) {

    return [
        'companypolicymasterID' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'code' => $faker->word,
        'documentID' => $faker->word,
        'isYN' => $faker->randomDigitNotNull,
        'value' => $faker->word,
        'createdUserGroup' => $faker->word,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->word,
        'modifiedPCID' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserID' => $faker->date('Y-m-d H:i:s'),
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
