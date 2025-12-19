<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EmployeeDesignation;
use Faker\Generator as Faker;

$factory->define(EmployeeDesignation::class, function (Faker $faker) {

    return [
        'EmpID' => $faker->randomDigitNotNull,
        'DesignationID' => $faker->randomDigitNotNull,
        'startDate' => $faker->word,
        'endDate' => $faker->word,
        'PrincipalCategoryID' => $faker->randomDigitNotNull,
        'SectionID' => $faker->randomDigitNotNull,
        'DepartmentID' => $faker->randomDigitNotNull,
        'isMajor' => $faker->randomDigitNotNull,
        'SubjectID' => $faker->randomDigitNotNull,
        'ClassID' => $faker->randomDigitNotNull,
        'GroupID' => $faker->word,
        'Erp_companyID' => $faker->randomDigitNotNull,
        'SchMasterID' => $faker->randomDigitNotNull,
        'BranchID' => $faker->randomDigitNotNull,
        'AcademicYearID' => $faker->randomDigitNotNull,
        'DateFrom' => $faker->word,
        'DateTo' => $faker->word,
        'isActive' => $faker->randomDigitNotNull,
        'CreatedUserName' => $faker->word,
        'CreatedDate' => $faker->date('Y-m-d H:i:s'),
        'CreatedPC' => $faker->word,
        'ModifiedUserName' => $faker->word,
        'Timestamp' => $faker->date('Y-m-d H:i:s'),
        'ModifiedPC' => $faker->word,
        'DesignationTypeID' => $faker->randomDigitNotNull
    ];
});
