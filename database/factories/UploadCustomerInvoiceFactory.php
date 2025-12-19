<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UploadCustomerInvoice;
use Faker\Generator as Faker;

$factory->define(UploadCustomerInvoice::class, function (Faker $faker) {

    return [
        'uploadComment' => $faker->word,
        'uploadedDate' => $faker->word,
        'uploadedBy' => $faker->word,
        'uploadStatus' => $faker->randomDigitNotNull,
        'counter' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
