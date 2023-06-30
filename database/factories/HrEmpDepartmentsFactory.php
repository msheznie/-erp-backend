<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HrEmpDepartments;
use Faker\Generator as Faker;

$factory->define(HrEmpDepartments::class, function (Faker $faker) {

    return [
        'AcademicYearID' => $faker->randomDigitNotNull,
        'BranchID' => $faker->randomDigitNotNull,
        'CreatedDate' => $faker->date('Y-m-d H:i:s'),
        'CreatedPC' => $faker->word,
        'CreatedUserName' => $faker->word,
        'date_from' => $faker->date('Y-m-d H:i:s'),
        'date_to' => $faker->date('Y-m-d H:i:s'),
        'DepartmentMasterID' => $faker->randomDigitNotNull,
        'EmpID' => $faker->randomDigitNotNull,
        'Erp_companyID' => $faker->randomDigitNotNull,
        'isActive' => $faker->randomDigitNotNull,
        'isPrimary' => $faker->randomDigitNotNull,
        'ModifiedPC' => $faker->word,
        'ModifiedUserName' => $faker->word,
        'SchMasterID' => $faker->randomDigitNotNull,
        'Timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
