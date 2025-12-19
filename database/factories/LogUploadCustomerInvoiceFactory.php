<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\LogUploadCustomerInvoice;
use Faker\Generator as Faker;

$factory->define(LogUploadCustomerInvoice::class, function (Faker $faker) {

    return [
        'customerInvoiceUploadID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'is_failed' => $faker->word,
        'log_message' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
