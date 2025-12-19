<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MonthlyDeclarationsTypes;
use Faker\Generator as Faker;

$factory->define(MonthlyDeclarationsTypes::class, function (Faker $faker) {

    return [
        'monthlyDeclaration' => $faker->word,
        'monthlyDeclarationType' => $faker->word,
        'salaryCategoryID' => $faker->randomDigitNotNull,
        'expenseGLCode' => $faker->randomDigitNotNull,
        'isPayrollCategory' => $faker->word,
        'companyID' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
