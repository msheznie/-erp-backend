<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EvacuationCriteriaScoreConfigLog;
use Faker\Generator as Faker;

$factory->define(EvacuationCriteriaScoreConfigLog::class, function (Faker $faker) {

    return [
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'criteria_detail_id' => $faker->randomDigitNotNull,
        'fromTender' => $faker->randomDigitNotNull,
        'id' => $faker->randomDigitNotNull,
        'is_deleted' => $faker->randomDigitNotNull,
        'label' => $faker->word,
        'level_no' => $faker->randomDigitNotNull,
        'score' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'version_id' => $faker->randomDigitNotNull
    ];
});
