<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AssetTransferReferredback;
use Faker\Generator as Faker;

$factory->define(AssetTransferReferredback::class, function (Faker $faker) {

    return [
        'id' => $faker->randomDigitNotNull,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'purchaseRequestCode' => $faker->word,
        'budgetYear' => $faker->randomDigitNotNull,
        'prBelongsYear' => $faker->randomDigitNotNull,
        'document_id' => $faker->word,
        'document_code' => $faker->word,
        'type' => $faker->randomDigitNotNull,
        'location' => $faker->randomDigitNotNull,
        'reference_no' => $faker->word,
        'document_date' => $faker->date('Y-m-d H:i:s'),
        'approval_comments' => $faker->word,
        'serial_no' => $faker->randomDigitNotNull,
        'emp_id' => $faker->randomDigitNotNull,
        'narration' => $faker->text,
        'refferedBackYN' => $faker->randomDigitNotNull,
        'serviceLineCode' => $faker->word,
        'company_id' => $faker->randomDigitNotNull,
        'company_code' => $faker->word,
        'confirmed_yn' => $faker->randomDigitNotNull,
        'confirmed_by_emp_id' => $faker->randomDigitNotNull,
        'confirmedByName' => $faker->word,
        'confirmedByEmpID' => $faker->word,
        'confirmed_date' => $faker->date('Y-m-d H:i:s'),
        'documentSystemID' => $faker->randomDigitNotNull,
        'approved_yn' => $faker->randomDigitNotNull,
        'approved_date' => $faker->date('Y-m-d H:i:s'),
        'approved_by_emp_name' => $faker->word,
        'approved_by_emp_id' => $faker->randomDigitNotNull,
        'current_level_no' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'created_user_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'purchaseRequestID' => $faker->randomDigitNotNull,
        'updated_user_id' => $faker->randomDigitNotNull
    ];
});
