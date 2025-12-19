<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderSupplierAssignee;
use Faker\Generator as Faker;

$factory->define(TenderSupplierAssignee::class, function (Faker $faker) {

    return [
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'registration_link_id' => $faker->randomDigitNotNull,
        'supplier_assigned_id' => $faker->randomDigitNotNull,
        'supplier_email' => $faker->word,
        'supplier_name' => $faker->word,
        'tender_master_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
