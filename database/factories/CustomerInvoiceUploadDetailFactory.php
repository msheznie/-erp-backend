<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomerInvoiceUploadDetail;
use Faker\Generator as Faker;

$factory->define(CustomerInvoiceUploadDetail::class, function (Faker $faker) {

    return [
        'companySystemID' => $faker->randomDigitNotNull,
        'customerInvoiceUploadID' => $faker->randomDigitNotNull,
        'custInvoiceDirectID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
