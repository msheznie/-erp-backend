<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\WorkOrderGenerationLog;
use Faker\Generator as Faker;

$factory->define(WorkOrderGenerationLog::class, function (Faker $faker) {

    return [
        'date' => $faker->date('Y-m-d H:i:s'),
        'createdUser' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
