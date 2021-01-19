<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SoPaymentTerms;
use Faker\Generator as Faker;

$factory->define(SoPaymentTerms::class, function (Faker $faker) {

    return [
        'paymentTermsCategory' => $faker->randomDigitNotNull,
        'soID' => $faker->randomDigitNotNull,
        'paymentTemDes' => $faker->word,
        'comAmount' => $faker->randomDigitNotNull,
        'comPercentage' => $faker->randomDigitNotNull,
        'inDays' => $faker->randomDigitNotNull,
        'comDate' => $faker->date('Y-m-d H:i:s'),
        'LCPaymentYN' => $faker->randomDigitNotNull,
        'isRequested' => $faker->randomDigitNotNull,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
