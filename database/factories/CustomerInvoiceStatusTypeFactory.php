<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomerInvoiceStatusType;
use Faker\Generator as Faker;

$factory->define(CustomerInvoiceStatusType::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'timestamp' => $faker->word
    ];
});
