<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ItemBatch;
use Faker\Generator as Faker;

$factory->define(ItemBatch::class, function (Faker $faker) {

    return [
        'itemSystemCode' => $faker->randomDigitNotNull,
        'batchCode' => $faker->word,
        'expireDate' => $faker->date('Y-m-d H:i:s'),
        'wareHouseSystemID' => $faker->randomDigitNotNull,
        'binLocation' => $faker->randomDigitNotNull,
        'soldFlag' => $faker->randomDigitNotNull,
        'quantity' => $faker->randomDigitNotNull,
        'copiedQty' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
