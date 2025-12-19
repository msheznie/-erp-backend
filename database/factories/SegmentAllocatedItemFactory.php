<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SegmentAllocatedItem;
use Faker\Generator as Faker;

$factory->define(SegmentAllocatedItem::class, function (Faker $faker) {

    return [
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentMasterAutoID' => $faker->randomDigitNotNull,
        'documentDetailAutoID' => $faker->randomDigitNotNull,
        'detailQty' => $faker->randomDigitNotNull,
        'allocatedQty' => $faker->randomDigitNotNull,
        'pulledDocumentSystemID' => $faker->randomDigitNotNull,
        'pulledDocumentDetailID' => $faker->randomDigitNotNull
    ];
});
