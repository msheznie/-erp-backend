<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentSubProduct;
use Faker\Generator as Faker;

$factory->define(DocumentSubProduct::class, function (Faker $faker) {

    return [
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'documentDetailID' => $faker->randomDigitNotNull,
        'productSerialID' => $faker->randomDigitNotNull,
        'productBatchID' => $faker->randomDigitNotNull,
        'quantity' => $faker->randomDigitNotNull,
        'sold' => $faker->randomDigitNotNull,
        'soldQty' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
