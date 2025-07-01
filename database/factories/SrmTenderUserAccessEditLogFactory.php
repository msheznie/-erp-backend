<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SrmTenderUserAccessEditLog;
use Faker\Generator as Faker;

$factory->define(SrmTenderUserAccessEditLog::class, function (Faker $faker) {

    return [
        'id' => $faker->randomDigitNotNull,
        'version_id' => $faker->randomDigitNotNull,
        'level_no' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'user_id' => $faker->randomDigitNotNull,
        'module_id' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
