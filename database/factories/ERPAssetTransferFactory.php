<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ERPAssetTransfer;
use Faker\Generator as Faker;

$factory->define(ERPAssetTransfer::class, function (Faker $faker) {

    return [
        'document_id' => $faker->word,
        'document_code' => $faker->word,
        'type' => $faker->word,
        'reference_no' => $faker->word,
        'document_date' => $faker->word,
        'approval_comments' => $faker->word,
        'serial_no' => $faker->randomDigitNotNull,
        'emp_id' => $faker->randomDigitNotNull,
        'narration' => $faker->text,
        'company_id' => $faker->randomDigitNotNull,
        'confirmed_yn' => $faker->randomDigitNotNull,
        'confirmed_by_emp_id' => $faker->randomDigitNotNull,
        'confirmed_by_name' => $faker->word,
        'confirmed_date' => $faker->date('Y-m-d H:i:s'),
        'approved_yn' => $faker->randomDigitNotNull,
        'approved_date' => $faker->date('Y-m-d H:i:s'),
        'approved_by_emp_name' => $faker->word,
        'approved_by_emp_id' => $faker->randomDigitNotNull,
        'current_level_no' => $faker->randomDigitNotNull,
        'created_user_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
