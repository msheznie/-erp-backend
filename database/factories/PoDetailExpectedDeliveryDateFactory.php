<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PoDetailExpectedDeliveryDate;
use Faker\Generator as Faker;

$factory->define(PoDetailExpectedDeliveryDate::class, function (Faker $faker) {

    return [
        'po_detail_auto_id' => $faker->randomDigitNotNull,
        'expected_delivery_date' => $faker->date('Y-m-d H:i:s'),
        'allocated_qty' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
