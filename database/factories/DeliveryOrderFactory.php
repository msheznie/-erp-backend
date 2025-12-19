<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DeliveryOrder;
use Faker\Generator as Faker;

$factory->define(DeliveryOrder::class, function (Faker $faker) {

    return [
        'orderType' => $faker->word,
        'deliveryOrderCode' => $faker->word,
        'companySystemId' => $faker->randomDigitNotNull,
        'documentSystemId' => $faker->randomDigitNotNull,
        'companyFinanceYearID' => $faker->randomDigitNotNull,
        'FYBiggin' => $faker->date('Y-m-d H:i:s'),
        'FYEnd' => $faker->date('Y-m-d H:i:s'),
        'companyFinancePeriodID' => $faker->randomDigitNotNull,
        'FYPeriodDateFrom' => $faker->date('Y-m-d H:i:s'),
        'FYPeriodDateTo' => $faker->date('Y-m-d H:i:s'),
        'deliveryOrderDate' => $faker->date('Y-m-d H:i:s'),
        'wareHouseSystemCode' => $faker->randomDigitNotNull,
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'referenceNo' => $faker->word,
        'customerID' => $faker->randomDigitNotNull,
        'salesPersonID' => $faker->randomDigitNotNull,
        'narration' => $faker->text,
        'notes' => $faker->text,
        'contactPersonNumber' => $faker->word,
        'contactPersonName' => $faker->word,
        'transactionCurrencyID' => $faker->randomDigitNotNull,
        'transactionCurrencyER' => $faker->randomDigitNotNull,
        'transactionAmount' => $faker->randomDigitNotNull,
        'companyLocalCurrencyID' => $faker->randomDigitNotNull,
        'companyLocalCurrencyER' => $faker->randomDigitNotNull,
        'companyLocalAmount' => $faker->randomDigitNotNull,
        'companyReportingCurrencyID' => $faker->randomDigitNotNull,
        'companyReportingCurrencyER' => $faker->randomDigitNotNull,
        'companyReportingAmount' => $faker->randomDigitNotNull,
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedByEmpSystemID' => $faker->randomDigitNotNull,
        'confirmedByEmpID' => $faker->word,
        'confirmedByName' => $faker->word,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedYN' => $faker->randomDigitNotNull,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedEmpSystemID' => $faker->randomDigitNotNull,
        'approvedbyEmpID' => $faker->word,
        'approvedbyEmpName' => $faker->word,
        'refferedBackYN' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'RollLevForApp_curr' => $faker->randomDigitNotNull,
        'closedYN' => $faker->randomDigitNotNull,
        'closedDate' => $faker->date('Y-m-d H:i:s'),
        'closedReason' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->randomDigitNotNull,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
