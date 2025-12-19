<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PdcLogPrintedHistory;
use Faker\Generator as Faker;

$factory->define(PdcLogPrintedHistory::class, function (Faker $faker) {

    return [
        'pdcLogID' => $faker->randomDigitNotNull,
        'chequePrintedBy' => $faker->randomDigitNotNull,
        'chequePrintedDate' => $faker->date('Y-m-d H:i:s'),
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
