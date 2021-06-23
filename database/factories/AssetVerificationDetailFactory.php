<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AssetVerificationDetail;
use Faker\Generator as Faker;

$factory->define(AssetVerificationDetail::class, function (Faker $faker) {

    return [
        'verification_id' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'faID' => $faker->randomDigitNotNull,
        'verifiedDate' => $faker->word,
        'narration' => $faker->text,
        'createdUserGroup' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdPcID' => $faker->word,
        'modifiedUser' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'modifiedPc' => $faker->word,
        'createdDateAndTime' => $faker->date('Y-m-d H:i:s'),
        'createdDateTime' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
