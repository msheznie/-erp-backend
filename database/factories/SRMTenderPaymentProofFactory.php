<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SRMTenderPaymentProof;
use Faker\Generator as Faker;

$factory->define(SRMTenderPaymentProof::class, function (Faker $faker) {

    return [
        'uuid' => $faker->word,
        'serial_no' => $faker->randomDigitNotNull,
        'document_system_id' => $faker->randomDigitNotNull,
        'document_id' => $faker->word,
        'document_code' => $faker->word,
        'company_id' => $faker->randomDigitNotNull,
        'tender_uuid' => $faker->word,
        'srm_supplier_uuid' => $faker->word,
        'confirmed_yn' => $faker->randomDigitNotNull,
        'confirmed_by_emp_system_id' => $faker->randomDigitNotNull,
        'confirmed_by_emp_id' => $faker->word,
        'confirmed_by_name' => $faker->word,
        'confirmed_date' => $faker->date('Y-m-d H:i:s'),
        'approved_yn' => $faker->randomDigitNotNull,
        'approved_date' => $faker->date('Y-m-d H:i:s'),
        'approved_emp_system_id' => $faker->randomDigitNotNull,
        'approved_by_emp_id' => $faker->word,
        'approved_by_emp_name' => $faker->word,
        'refferedBackYN' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'RollLevForApp_curr' => $faker->randomDigitNotNull
    ];
});
