<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\FinalReturnIncomeReports;
use Faker\Generator as Faker;

$factory->define(FinalReturnIncomeReports::class, function (Faker $faker) {

    return [
        'report_name' => $faker->word,
        'template_id' => $faker->word,
        'financialyear_id' => $faker->word,
        'companySystemID' => $faker->randomDigitNotNull,
        'confirmedYN' => $faker->word,
        'confirmedByEmpSystemID' => $faker->word,
        'confirmedByEmpID' => $faker->word,
        'confirmedByName' => $faker->word,
        'confirmedDate' => $faker->date('Y-m-d H:i:s'),
        'submittedYN' => $faker->word,
        'submittedByEmpSystemID' => $faker->word,
        'submittedByEmpID' => $faker->word,
        'submittedByName' => $faker->word,
        'submittedDate' => $faker->date('Y-m-d H:i:s'),
        'createdPCID' => $faker->word,
        'createdUserSystemID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedPCID' => $faker->word,
        'modifiedUserSystemID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
