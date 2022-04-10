<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderBoqItems;
use Faker\Generator as Faker;

$factory->define(TenderBoqItems::class, function (Faker $faker) {

    return [
        'main_work_id' => $faker->randomDigitNotNull,
        'item_id' => $faker->randomDigitNotNull,
        'uom' => $faker->randomDigitNotNull,
        'qty' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull
    ];
});
