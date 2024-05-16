<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SRMTenderUserAccess;
use Faker\Generator as Faker;

$factory->define(SRMTenderUserAccess::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'user_id' => $faker->randomDigitNotNull,
        'module_id' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
