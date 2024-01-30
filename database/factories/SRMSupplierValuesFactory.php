<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SRMSupplierValues;
use Faker\Generator as Faker;

$factory->define(SRMSupplierValues::class, function (Faker $faker) {

    return [
        'user_name' => $faker->word,
        'name' => $faker->word,
        'uuid' => $faker->word,
        'company_id' => $faker->randomDigitNotNull,
        'supplier_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
