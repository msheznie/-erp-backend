<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SegmentAssigned;
use Faker\Generator as Faker;

$factory->define(SegmentAssigned::class, function (Faker $faker) {

    return [
        'serviceLineSystemID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'isActive' => $faker->word,
        'isAssigned' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
