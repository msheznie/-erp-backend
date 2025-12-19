<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SupplierBlock;
use Faker\Generator as Faker;

$factory->define(SupplierBlock::class, function (Faker $faker) {

    return [
        'supplierCodeSytem' => $faker->randomDigitNotNull,
        'blockType' => $faker->word,
        'blockFrom' => $faker->word,
        'blockTo' => $faker->word,
        'blockReason' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
