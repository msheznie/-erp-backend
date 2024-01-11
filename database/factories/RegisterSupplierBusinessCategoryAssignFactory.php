<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RegisterSupplierBusinessCategoryAssign;
use Faker\Generator as Faker;

$factory->define(RegisterSupplierBusinessCategoryAssign::class, function (Faker $faker) {

    return [
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'supCategoryMasterID' => $faker->randomDigitNotNull,
        'supplierID' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
