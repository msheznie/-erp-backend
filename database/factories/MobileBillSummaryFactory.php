<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MobileBillSummary;
use Faker\Generator as Faker;

$factory->define(MobileBillSummary::class, function (Faker $faker) {

    return [
        'mobileMasterID' => $faker->randomDigitNotNull,
        'mobileNumber' => $faker->randomDigitNotNull,
        'rental' => $faker->randomDigitNotNull,
        'setUpFee' => $faker->randomDigitNotNull,
        'localCharges' => $faker->randomDigitNotNull,
        'internationalCallCharges' => $faker->randomDigitNotNull,
        'domesticSMS' => $faker->randomDigitNotNull,
        'internationalSMS' => $faker->randomDigitNotNull,
        'domesticMMS' => $faker->randomDigitNotNull,
        'internationalMMS' => $faker->randomDigitNotNull,
        'discounts' => $faker->randomDigitNotNull,
        'otherCharges' => $faker->randomDigitNotNull,
        'blackberryCharges' => $faker->randomDigitNotNull,
        'roamingCharges' => $faker->randomDigitNotNull,
        'GPRSPayG' => $faker->randomDigitNotNull,
        'GPRSPKG' => $faker->randomDigitNotNull,
        'totalCurrentCharges' => $faker->randomDigitNotNull,
        'billDate' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
