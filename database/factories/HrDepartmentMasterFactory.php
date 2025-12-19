<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HrDepartmentMaster;
use Faker\Generator as Faker;

$factory->define(HrDepartmentMaster::class, function (Faker $faker) {

    return [
        'BranchID' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'CreatedDate' => $faker->date('Y-m-d H:i:s'),
        'CreatedPC' => $faker->word,
        'CreatedUserName' => $faker->word,
        'DepartmentDes' => $faker->word,
        'Erp_companyID' => $faker->randomDigitNotNull,
        'hod_id' => $faker->randomDigitNotNull,
        'isActive' => $faker->randomDigitNotNull,
        'ModifiedPC' => $faker->word,
        'ModifiedUserName' => $faker->word,
        'SchMasterID' => $faker->randomDigitNotNull,
        'SortOrder' => $faker->randomDigitNotNull,
        'Timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
