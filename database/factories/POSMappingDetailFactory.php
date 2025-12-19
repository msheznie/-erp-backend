<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSMappingDetail;
use Faker\Generator as Faker;

$factory->define(POSMappingDetail::class, function (Faker $faker) {

    return [
        'master_id' => $faker->randomDigitNotNull,
        'table' => $faker->word,
        'key' => $faker->word
    ];
});
