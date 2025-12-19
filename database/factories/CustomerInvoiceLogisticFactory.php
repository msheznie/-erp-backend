<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomerInvoiceLogistic;
use Faker\Generator as Faker;

$factory->define(CustomerInvoiceLogistic::class, function (Faker $faker) {

    return [
        'custInvoiceDirectAutoID' => $faker->randomDigitNotNull,
        'consignee_name' => $faker->word,
        'consignee_contact_no' => $faker->word,
        'consignee_address' => $faker->text,
        'vessel_no' => $faker->word,
        'b_ladding_no' => $faker->word,
        'port_of_loading' => $faker->randomDigitNotNull,
        'port_of_discharge' => $faker->randomDigitNotNull,
        'no_of_container' => $faker->word,
        'delivery_payment' => $faker->text,
        'payment_terms' => $faker->text,
        'is_deleted' => $faker->word,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
