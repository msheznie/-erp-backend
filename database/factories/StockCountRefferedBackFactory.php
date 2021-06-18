<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\StockCountRefferedBack;
use Faker\Generator as Faker;

$factory->define(StockCountRefferedBack::class, function (Faker $faker) {

    return [
        'stockCountAutoID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'serviceLineCode' => $faker->word,
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentID' => $faker->word,
        'companyFinanceYearID' => $faker->randomDigitNotNull,
        'companyFinancePeriodID' => $faker->randomDigitNotNull,
        'FYBiggin' => $faker->date('Y-m-d H:i:s'),
        'FYEnd' => $faker->date('Y-m-d H:i:s'),
        'serialNo' => $faker->randomDigitNotNull,
        'stockCountCode' => $faker->word,
        'refNo' => $faker->word,
        'stockCountDate' => $faker->date('Y-m-d H:i:s'),
        'location' => $faker->randomDigitNotNull,
        'comment' => $faker->word,
        'stockCountType' => $faker->word,
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedByEmpSystemID' => $faker->randomDigitNotNull,
        'confirmedByEmpID' => $faker->word,
        'confirmedByName' => $faker->word,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'approved' => $faker->randomDigitNotNull,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedByUserID' => $faker->word,
        'approvedByUserSystemID' => $faker->randomDigitNotNull,
        'refferedBackYN' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserGroup' => $faker->word,
        'createdPCid' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdUserID' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'modifiedUser' => $faker->word,
        'modifiedPc' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'RollLevForApp_curr' => $faker->randomDigitNotNull
    ];
});
