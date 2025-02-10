<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocCodeSetupCommon;
use Faker\Generator as Faker;

$factory->define(DocCodeSetupCommon::class, function (Faker $faker) {

    return [
        'master_id' => $faker->randomDigitNotNull,
        'document_transaction_id' => $faker->randomDigitNotNull,
        'format1' => $faker->randomDigitNotNull,
        'format2' => $faker->randomDigitNotNull,
        'format3' => $faker->randomDigitNotNull,
        'format4' => $faker->randomDigitNotNull,
        'format5' => $faker->randomDigitNotNull,
        'format6' => $faker->randomDigitNotNull,
        'format7' => $faker->randomDigitNotNull,
        'format8' => $faker->randomDigitNotNull,
        'format9' => $faker->randomDigitNotNull,
        'format10' => $faker->randomDigitNotNull,
        'format11' => $faker->randomDigitNotNull,
        'format12' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
