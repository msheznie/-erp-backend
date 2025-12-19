<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SrmBidDocumentattachments;
use Faker\Generator as Faker;

$factory->define(SrmBidDocumentattachments::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentID' => $faker->randomDigitNotNull,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'attachmentDescription' => $faker->word,
        'originalFileName' => $faker->word,
        'myFileName' => $faker->word,
        'path' => $faker->word,
        'sizeInKbs' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
