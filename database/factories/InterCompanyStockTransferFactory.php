<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\InterCompanyStockTransfer;
use Faker\Generator as Faker;

$factory->define(InterCompanyStockTransfer::class, function (Faker $faker) {

    return [
        'stockTransferID' => $faker->randomDigitNotNull,
        'customerInvoiceID' => $faker->randomDigitNotNull,
        'stockReceiveID' => $faker->randomDigitNotNull,
        'supplierInvoiceID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
