<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use Faker\Generator as Faker;

$factory->define(SrmTenderBidEmployeeDetailsEditLog::class, function (Faker $faker) {

    return [
        'commercial_eval_remarks' => $faker->word,
        'commercial_eval_status' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'emp_id' => $faker->randomDigitNotNull,
        'modify_type' => $faker->randomDigitNotNull,
        'remarks' => $faker->word,
        'status' => $faker->word,
        'tender_award_commite_mem_comment' => $faker->word,
        'tender_award_commite_mem_status' => $faker->word,
        'tender_edit_version_id' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
