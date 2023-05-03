<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentAttachmentsEditLog;
use Faker\Generator as Faker;

$factory->define(DocumentAttachmentsEditLog::class, function (Faker $faker) {

    return [
        'approvalLevelOrder' => $faker->randomDigitNotNull,
        'attachmentDescription' => $faker->word,
        'attachmentType' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'docExpirtyDate' => $faker->word,
        'documentID' => $faker->word,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'envelopType' => $faker->randomDigitNotNull,
        'isUploaded' => $faker->randomDigitNotNull,
        'master_id' => $faker->randomDigitNotNull,
        'modify_type' => $faker->randomDigitNotNull,
        'myFileName' => $faker->word,
        'originalFileName' => $faker->word,
        'parent_id' => $faker->randomDigitNotNull,
        'path' => $faker->word,
        'pullFromAnotherDocument' => $faker->randomDigitNotNull,
        'ref_log_id' => $faker->randomDigitNotNull,
        'sizeInKbs' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
