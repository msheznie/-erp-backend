<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderNegotiationApproval;
use Faker\Generator as Faker;

$factory->define(TenderNegotiationApproval::class, function (Faker $faker) {

    return [
        'emp_id' => $faker->randomDigitNotNull,
        'tender_negotiation_id' => $faker->randomDigitNotNull,
        'status' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
