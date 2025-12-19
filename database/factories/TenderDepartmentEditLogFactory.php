<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderDepartmentEditLog;
use Faker\Generator as Faker;

$factory->define(TenderDepartmentEditLog::class, function (Faker $faker) {

    return [
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'department_id' => $faker->randomDigitNotNull,
        'id' => $faker->randomDigitNotNull,
        'level_no' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'version_id' => $faker->randomDigitNotNull
    ];
});
