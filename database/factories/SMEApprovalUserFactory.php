<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMEApprovalUser;
use Faker\Generator as Faker;

$factory->define(SMEApprovalUser::class, function (Faker $faker) {

    return [
        'companyCode' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdPCID' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'createdUserName' => $faker->word,
        'delegated_from' => $faker->date('Y-m-d H:i:s'),
        'delegated_to' => $faker->date('Y-m-d H:i:s'),
        'delegation_master_id' => $faker->randomDigitNotNull,
        'delegator' => $faker->randomDigitNotNull,
        'deligation_detail_id' => $faker->randomDigitNotNull,
        'designation' => $faker->word,
        'document' => $faker->word,
        'documentID' => $faker->word,
        'employeeID' => $faker->word,
        'employeeName' => $faker->word,
        'fromAmount' => $faker->randomDigitNotNull,
        'groupID' => $faker->randomDigitNotNull,
        'levelNo' => $faker->randomDigitNotNull,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedUserName' => $faker->word,
        'Status' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'toAmount' => $faker->randomDigitNotNull
    ];
});
