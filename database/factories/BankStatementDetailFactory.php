<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BankStatementDetail;
use Faker\Generator as Faker;

$factory->define(BankStatementDetail::class, function (Faker $faker) {

    return [
        'statementId' => $faker->randomDigitNotNull,
        'transactionNumber' => $faker->word,
        'transactionDate' => $faker->word,
        'debit' => $faker->randomDigitNotNull,
        'credit' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'category' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'timeStamp' => $faker->date('Y-m-d H:i:s')
    ];
});
