<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ProjectGlDetail;
use Faker\Generator as Faker;

$factory->define(ProjectGlDetail::class, function (Faker $faker) {

    return [
        'projectID' => $faker->randomDigitNotNull,
        'chartOfAccountSystemID' => $faker->randomDigitNotNull,
        'companySystemID' => $faker->randomDigitNotNull,
        'amount' => $faker->randomDigitNotNull,
        'createdBy' => $faker->randomDigitNotNull,
        'updatedBy' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
