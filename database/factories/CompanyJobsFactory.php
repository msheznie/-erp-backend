<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CompanyJobs;
use Faker\Generator as Faker;

$factory->define(CompanyJobs::class, function (Faker $faker) {

    return [
        'system_job_id' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'is_active' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
