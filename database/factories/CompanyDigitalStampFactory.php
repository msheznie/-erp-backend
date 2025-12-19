<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CompanyDigitalStamp;
use Faker\Generator as Faker;

$factory->define(CompanyDigitalStamp::class, function (Faker $faker) {

    return [
        'path' => $faker->word,
        'company_system_id' => $faker->randomDigitNotNull,
        'is_default' => $faker->word,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
