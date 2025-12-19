<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MrBulkUploadErrorLog;
use Faker\Generator as Faker;

$factory->define(MrBulkUploadErrorLog::class, function (Faker $faker) {

    return [
        'documentSystemID' => $faker->randomDigitNotNull,
        'error' => $faker->text,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
