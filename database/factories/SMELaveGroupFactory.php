<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMELaveGroup;
use Faker\Generator as Faker;

$factory->define(SMELaveGroup::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'isMonthly' => $faker->randomDigitNotNull,
        'isDefault' => $faker->randomDigitNotNull,
        'approvalLevels' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s')
    ];
});
