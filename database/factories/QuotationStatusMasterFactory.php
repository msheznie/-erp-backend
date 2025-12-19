<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\QuotationStatusMaster;
use Faker\Generator as Faker;

$factory->define(QuotationStatusMaster::class, function (Faker $faker) {

    return [
        'quotationStatus' => $faker->word,
        'isAdmin' => $faker->word
    ];
});
