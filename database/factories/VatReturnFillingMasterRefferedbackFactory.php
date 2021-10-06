<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VatReturnFillingMasterRefferedback;
use Faker\Generator as Faker;

$factory->define(VatReturnFillingMasterRefferedback::class, function (Faker $faker) {

    return [
        'returnFillingID' => $faker->randomDigitNotNull,
        'returnFillingCode' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'documentSystemID' => $faker->randomDigitNotNull,
        'date' => $faker->date('Y-m-d H:i:s'),
        'comment' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'confirmedYN' => $faker->randomDigitNotNull,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'confirmedByEmpSystemID' => $faker->randomDigitNotNull,
        'confirmedByEmpID' => $faker->word,
        'confirmedByEmpName' => $faker->word,
        'approvedYN' => $faker->randomDigitNotNull,
        'approvedDate' => $faker->date('Y-m-d H:i:s'),
        'approvedByUserSystemID' => $faker->randomDigitNotNull,
        'approvedEmpID' => $faker->word,
        'refferedBackYN' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'RollLevForApp_curr' => $faker->randomDigitNotNull,
        'serialNo' => $faker->randomDigitNotNull
    ];
});
