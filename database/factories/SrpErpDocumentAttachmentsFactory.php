<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SrpErpDocumentAttachments;
use Faker\Generator as Faker;

$factory->define(SrpErpDocumentAttachments::class, function (Faker $faker) {

    return [
        'documentID' => $faker->word,
        'documentSubID' => $faker->word,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'attachmentDescription' => $faker->text,
        'myFileName' => $faker->text,
        'docExpiryDate' => $faker->word,
        'dateofIssued' => $faker->word,
        'fileType' => $faker->word,
        'fileSize' => $faker->randomDigitNotNull,
        'segmentID' => $faker->randomDigitNotNull,
        'segmentCode' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
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
