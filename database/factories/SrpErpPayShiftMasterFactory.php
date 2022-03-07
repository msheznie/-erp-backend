<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SrpErpPayShiftMaster;
use Faker\Generator as Faker;

$factory->define(SrpErpPayShiftMaster::class, function (Faker $faker) {

    return [
        'Description' => $faker->word,
        'isFlexyHour' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'isDefault' => $faker->word,
        'companyCode' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
