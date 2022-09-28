<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\JobErrorLog;
use Faker\Generator as Faker;

$factory->define(JobErrorLog::class, function (Faker $faker) {

    return [
        'documentSystemID' => $faker->randomDigitNotNull,
        'documentSystemCode' => $faker->randomDigitNotNull,
        'tag' => $faker->word,
        'errorType' => $faker->randomDigitNotNull,
        'errorMessage' => $faker->text,
        'error' => $faker->text,
        'status' => $faker->randomDigitNotNull,
        'updatedBy' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
