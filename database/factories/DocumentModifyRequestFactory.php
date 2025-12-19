<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentModifyRequest;
use Faker\Generator as Faker;

$factory->define(DocumentModifyRequest::class, function (Faker $faker) {

    return [
        'approved' => $faker->word,
        'approved_by_user_system_id' => $faker->randomDigitNotNull,
        'approved_date' => $faker->date('Y-m-d H:i:s'),
        'companySystemID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'document_master_id' => $faker->randomDigitNotNull,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'rejected' => $faker->word,
        'rejected_by_user_system_id' => $faker->randomDigitNotNull,
        'rejected_date' => $faker->date('Y-m-d H:i:s'),
        'requested_date' => $faker->date('Y-m-d H:i:s'),
        'requested_document_master_id' => $faker->randomDigitNotNull,
        'requested_employeeSystemID' => $faker->randomDigitNotNull,
        'RollLevForApp_curr' => $faker->randomDigitNotNull,
        'status' => $faker->word,
        'type' => $faker->word,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'version' => $faker->randomDigitNotNull
    ];
});
