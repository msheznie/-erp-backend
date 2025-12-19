<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ProcumentActivity;
use Faker\Generator as Faker;

$factory->define(ProcumentActivity::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'category_id' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull
    ];
});
