<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MobileDetail;
use Faker\Generator as Faker;

$factory->define(MobileDetail::class, function (Faker $faker) {

    return [
        'mobilebillMasterID' => $faker->randomDigitNotNull,
        'billPeriod' => $faker->randomDigitNotNull,
        'startDate' => $faker->date('Y-m-d H:i:s'),
        'EndDate' => $faker->date('Y-m-d H:i:s'),
        'myNumber' => $faker->randomDigitNotNull,
        'DestCountry' => $faker->word,
        'DestNumber' => $faker->word,
        'duration' => $faker->word,
        'callDate' => $faker->date('Y-m-d H:i:s'),
        'cost' => $faker->randomDigitNotNull,
        'currency' => $faker->randomDigitNotNull,
        'Narration' => $faker->word,
        'localCurrencyID' => $faker->randomDigitNotNull,
        'localCurrencyER' => $faker->randomDigitNotNull,
        'localAmount' => $faker->randomDigitNotNull,
        'rptCurrencyID' => $faker->randomDigitNotNull,
        'rptCurrencyER' => $faker->randomDigitNotNull,
        'rptAmount' => $faker->randomDigitNotNull,
        'isOfficial' => $faker->randomDigitNotNull,
        'isIDD' => $faker->randomDigitNotNull,
        'type' => $faker->randomDigitNotNull,
        'userComments' => $faker->text,
        'createDate' => $faker->date('Y-m-d H:i:s'),
        'createUserID' => $faker->word,
        'createPCID' => $faker->word,
        'modifiedpc' => $faker->word,
        'modifiedUser' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
