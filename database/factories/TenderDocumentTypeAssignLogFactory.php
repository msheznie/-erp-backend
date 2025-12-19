<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderDocumentTypeAssignLog;
use Faker\Generator as Faker;

$factory->define(TenderDocumentTypeAssignLog::class, function (Faker $faker) {

    return [
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'document_type_id' => $faker->randomDigitNotNull,
        'master_id' => $faker->randomDigitNotNull,
        'modify_type' => $faker->randomDigitNotNull,
        'ref_log_id' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'version_id' => $faker->randomDigitNotNull
    ];
});
