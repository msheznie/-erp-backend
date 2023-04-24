<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ProcumentActivityEditLog;
use Faker\Generator as Faker;

$factory->define(ProcumentActivityEditLog::class, function (Faker $faker) {

    return [
        'tender_id' => $faker->randomDigitNotNull,
        'category_id' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'version_id' => $faker->randomDigitNotNull,
        'modify_type' => $faker->randomDigitNotNull,
        'master_id' => $faker->randomDigitNotNull,
        'ref_log_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
