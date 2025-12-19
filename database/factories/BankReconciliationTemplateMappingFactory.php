<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BankReconciliationTemplateMapping;
use Faker\Generator as Faker;

$factory->define(BankReconciliationTemplateMapping::class, function (Faker $faker) {

    return [
        'bankAccountAutoID' => $faker->randomDigitNotNull,
        'bankmasterAutoID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'bankName' => $faker->word,
        'bankAccount' => $faker->word,
        'statementStartDate' => $faker->word,
        'statementEndDate' => $faker->word,
        'bankReconciliationMonth' => $faker->word,
        'bankStatementDate' => $faker->word,
        'openingBalance' => $faker->word,
        'endingBalance' => $faker->word,
        'firstLine' => $faker->word,
        'headerLine' => $faker->word,
        'transactionNumber' => $faker->word,
        'transactionDate' => $faker->word,
        'debit' => $faker->word,
        'credit' => $faker->word,
        'description' => $faker->word,
        'category' => $faker->word,
        'account' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'timeStamp' => $faker->date('Y-m-d H:i:s')
    ];
});
