<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SupplierBusinessCategoryAssign;
use Faker\Generator as Faker;

$factory->define(SupplierBusinessCategoryAssign::class, function (Faker $faker) {

    return [
        'supplierID' => $faker->randomDigitNotNull,
        'supCategoryMasterID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
