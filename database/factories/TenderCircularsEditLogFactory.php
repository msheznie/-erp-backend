<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderCircularsEditLog;
use Faker\Generator as Faker;

$factory->define(TenderCircularsEditLog::class, function (Faker $faker) {

    return [
        'attachment_id' => $faker->randomDigitNotNull,
        'circular_name' => $faker->word,
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'description' => $faker->word,
        'master_id' => $faker->randomDigitNotNull,
        'modify_type' => $faker->randomDigitNotNull,
        'ref_log_id' => $faker->randomDigitNotNull,
        'status' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'vesion_id' => $faker->randomDigitNotNull
    ];
});
