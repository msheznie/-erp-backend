<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderEditLogMaster;
use Faker\Generator as Faker;

$factory->define(TenderEditLogMaster::class, function (Faker $faker) {

    return [
        'approved' => $faker->word,
        'approved_by_user_system_id' => $faker->randomDigitNotNull,
        'approved_date' => $faker->date('Y-m-d H:i:s'),
        'companyID' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'departmentID' => $faker->word,
        'departmentSystemID' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'documentCode' => $faker->word,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'employeeID' => $faker->word,
        'employeeSystemID' => $faker->randomDigitNotNull,
        'status' => $faker->word,
        'type' => $faker->word,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'version' => $faker->randomDigitNotNull
    ];
});
