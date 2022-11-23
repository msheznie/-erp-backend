<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSMappingMaster;
use Faker\Generator as Faker;

$factory->define(POSMappingMaster::class, function (Faker $faker) {

    return [
        'key' => $faker->word
    ];
});
