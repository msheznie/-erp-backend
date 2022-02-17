<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\InterCompanyAssetDisposal;
use Faker\Generator as Faker;

$factory->define(InterCompanyAssetDisposal::class, function (Faker $faker) {

    return [
        'assetDisposalID' => $faker->randomDigitNotNull,
        'customerInvoiceID' => $faker->randomDigitNotNull,
        'grvID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
