<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SupplierTenderNegotiation;
use Faker\Generator as Faker;

$factory->define(SupplierTenderNegotiation::class, function (Faker $faker) {

    return [
        'tender_negotiation_id' => $faker->randomDigitNotNull,
        'suppliermaster_id' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
