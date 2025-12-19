<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderPaymentDetail;
use Faker\Generator as Faker;

$factory->define(TenderPaymentDetail::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'srm_supplier_id' => $faker->randomDigitNotNull,
        'payment_method' => $faker->word,
        'payment_id' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'deleted_at' => $faker->date('Y-m-d H:i:s'),
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
