<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\B2BSubmissionFileDetail;
use Faker\Generator as Faker;

$factory->define(B2BSubmissionFileDetail::class, function (Faker $faker) {

    return [
        'bank_transfer_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'document_date' => $faker->word,
        'latest_downloaded_id' => $faker->randomDigitNotNull,
        'latest_submitted_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
