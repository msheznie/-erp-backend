<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SourceCustomerTypeMaster;
use Faker\Generator as Faker;

$factory->define(SourceCustomerTypeMaster::class, function (Faker $faker) {

    return [
        'customerDescription' => $faker->word,
        'displayDescription' => $faker->word,
        'isThirdPartyDelivery' => $faker->randomDigitNotNull,
        'isDineIn' => $faker->randomDigitNotNull,
        'isDefault' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'createdBy' => $faker->word,
        'createdDatetime' => $faker->date('Y-m-d H:i:s'),
        'createdPc' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'imageName' => $faker->word,
        'transaction_log_id' => $faker->randomDigitNotNull
    ];
});
