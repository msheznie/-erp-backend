<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ItemSerial;
use Faker\Generator as Faker;

$factory->define(ItemSerial::class, function (Faker $faker) {

    return [
        'itemSystemCode' => $faker->randomDigitNotNull,
        'productBatchID' => $faker->randomDigitNotNull,
        'serialCode' => $faker->word,
        'expireDate' => $faker->word,
        'wareHouseSystemID' => $faker->randomDigitNotNull,
        'binLocation' => $faker->randomDigitNotNull,
        'soldFlag' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
