<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HRDocumentApproved;
use Faker\Generator as Faker;

$factory->define(HRDocumentApproved::class, function (Faker $faker) {

    return [
        'wareHouseAutoID' => $faker->randomDigitNotNull,
        'departmentID' => $faker->word,
        'documentID' => $faker->word,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'documentCode' => $faker->word,
        'isCancel' => $faker->randomDigitNotNull,
        'documentDate' => $faker->date('Y-m-d H:i:s'),
        'approvalLevelID' => $faker->randomDigitNotNull,
        'isReverseApplicableYN' => $faker->randomDigitNotNull,
        'roleID' => $faker->randomDigitNotNull,
        'leaveSetupID' => $faker->randomDigitNotNull,
        'approvalGroupID' => $faker->randomDigitNotNull,
        'roleLevelOrder' => $faker->randomDigitNotNull,
        'docConfirmedDate' => $faker->date('Y-m-d H:i:s'),
        'docConfirmedByEmpID' => $faker->word,
        'table_name' => $faker->word,
        'table_unique_field_name' => $faker->word,
        'approvedEmpID' => $faker->word,
        'approvedYN' => $faker->randomDigitNotNull,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedComments' => $faker->text,
        'approvedPC' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'timeStamp' => $faker->date('Y-m-d H:i:s'),
        'is_sync' => $faker->randomDigitNotNull,
        'id_store' => $faker->randomDigitNotNull
    ];
});
