<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\logUploadBudget;
use Faker\Generator as Faker;

$factory->define(logUploadBudget::class, function (Faker $faker) {

    return [
        'bugdet_upload_id' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'is_failed' => $faker->word,
        'error_line' => $faker->randomDigitNotNull,
        'log_message' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
