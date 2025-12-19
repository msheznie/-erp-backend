<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RegisterSupplierSubcategoryAssign;
use Faker\Generator as Faker;

$factory->define(RegisterSupplierSubcategoryAssign::class, function (Faker $faker) {

    return [
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'supplierID' => $faker->randomDigitNotNull,
        'supSubCategoryID' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
