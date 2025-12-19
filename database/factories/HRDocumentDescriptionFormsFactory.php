<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HRDocumentDescriptionForms;
use Faker\Generator as Faker;

$factory->define(HRDocumentDescriptionForms::class, function (Faker $faker) {

    return [
        'DocDesSetID' => $faker->randomDigitNotNull,
        'DocDesID' => $faker->randomDigitNotNull,
        'subDocumentType' => $faker->randomDigitNotNull,
        'FormType' => $faker->word,
        'PersonType' => $faker->word,
        'PersonID' => $faker->randomDigitNotNull,
        'FileName' => $faker->text,
        'UploadedDate' => $faker->date('Y-m-d H:i:s'),
        'issueDate' => $faker->word,
        'expireDate' => $faker->word,
        'issuedBy' => $faker->randomDigitNotNull,
        'issuedByText' => $faker->word,
        'documentNo' => $faker->word,
        'isActive' => $faker->randomDigitNotNull,
        'isDeleted' => $faker->randomDigitNotNull,
        'isExpiryMailSend' => $faker->randomDigitNotNull,
        'SchMasterID' => $faker->randomDigitNotNull,
        'BranchID' => $faker->randomDigitNotNull,
        'Erp_companyID' => $faker->randomDigitNotNull,
        'AcademicYearID' => $faker->randomDigitNotNull,
        'isSubmitted' => $faker->randomDigitNotNull,
        'CreatedUserID' => $faker->randomDigitNotNull,
        'CreatedUserName' => $faker->word,
        'CreatedDate' => $faker->date('Y-m-d H:i:s'),
        'CreatedPC' => $faker->word,
        'ModifiedUserID' => $faker->randomDigitNotNull,
        'ModifiedUserName' => $faker->word,
        'ModifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'ModifiedPC' => $faker->word,
        'Timestamp' => $faker->date('Y-m-d H:i:s')
    ];
});
