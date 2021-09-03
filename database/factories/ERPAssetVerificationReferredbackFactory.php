<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ERPAssetVerificationReferredback;
use Faker\Generator as Faker;

$factory->define(ERPAssetVerificationReferredback::class, function (Faker $faker) {

    return [
        'id' => $faker->randomDigitNotNull,
        'documentDate' => $faker->date('Y-m-d H:i:s'),
        'companySystemID' => $faker->randomDigitNotNull,
        'verficationCode' => $faker->word,
        'companyID' => $faker->word,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'serialNo' => $faker->randomDigitNotNull,
        'narration' => $faker->text,
        'RollLevForApp_curr' => $faker->word,
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedByEmpSystemID' => $faker->randomDigitNotNull,
        'confirmedByName' => $faker->word,
        'confirmedByEmpID' => $faker->word,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'approved' => $faker->randomDigitNotNull,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedByUserID' => $faker->word,
        'approvedByUserSystemID' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'refferedBackYN' => $faker->word,
        'createdUserGroup' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdPcID' => $faker->word,
        'modifiedUser' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'modifiedPc' => $faker->word,
        'createdDateAndTime' => $faker->date('Y-m-d H:i:s'),
        'createdDateTime' => $faker->word,
        'deleted_at' => $faker->date('Y-m-d H:i:s'),
        'deleteComment' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
