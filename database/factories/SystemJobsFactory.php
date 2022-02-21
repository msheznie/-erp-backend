<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SystemJobs;
use Faker\Generator as Faker;

$factory->define(SystemJobs::class, function (Faker $faker) {

    return [
        'job_description' => $faker->word,
        'job_signature' => $faker->word,
        'is_active' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
