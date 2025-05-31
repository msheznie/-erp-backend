<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BankReconciliationRules;
use Faker\Generator as Faker;

$factory->define(BankReconciliationRules::class, function (Faker $faker) {

    return [
        'bankAccountAutoID' => $faker->randomDigitNotNull,
        'ruleDescription' => $faker->word,
        'transactionType' => $faker->randomDigitNotNull,
        'matchType' => $faker->randomDigitNotNull,
        'isMatchAmount' => $faker->randomDigitNotNull,
        'amountDifference' => $faker->randomDigitNotNull,
        'isMatchDate' => $faker->randomDigitNotNull,
        'dateDifference' => $faker->randomDigitNotNull,
        'isMatchDocument' => $faker->randomDigitNotNull,
        'systemDocumentColumn' => $faker->randomDigitNotNull,
        'statementDocumentColumn' => $faker->randomDigitNotNull,
        'statementReferenceFrom' => $faker->randomDigitNotNull,
        'statementReferenceTo' => $faker->randomDigitNotNull,
        'isMatchChequeNo' => $faker->randomDigitNotNull,
        'statementChqueColumn' => $faker->randomDigitNotNull,
        'isDefault' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'timeStamp' => $faker->date('Y-m-d H:i:s')
    ];
});
