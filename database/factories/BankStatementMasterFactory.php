<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BankStatementMaster;
use Faker\Generator as Faker;

$factory->define(BankStatementMaster::class, function (Faker $faker) {

    return [
        'bankAccountAutoID' => $faker->randomDigitNotNull,
        'bankmasterAutoID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'companyID' => $faker->word,
        'transactionCount' => $faker->randomDigitNotNull,
        'statementStartDate' => $faker->word,
        'statementEndDate' => $faker->word,
        'bankReconciliationMonth' => $faker->word,
        'bankStatementDate' => $faker->word,
        'openingBalance' => $faker->randomDigitNotNull,
        'endingBalance' => $faker->randomDigitNotNull,
        'documentStatus' => $faker->randomDigitNotNull,
        'importStatus' => $faker->randomDigitNotNull,
        'importError' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'timeStamp' => $faker->date('Y-m-d H:i:s')
    ];
});
