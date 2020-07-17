<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\QuotationStatus;
use Faker\Generator as Faker;

$factory->define(QuotationStatus::class, function (Faker $faker) {

    return [
        'quotationID' => $faker->randomDigitNotNull,
        'quotationStatusMasterID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'quotationStatusDate' => $faker->date('Y-m-d H:i:s'),
        'comments' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'modifiedUserSystemID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
