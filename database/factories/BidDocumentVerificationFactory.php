<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BidDocumentVerification;
use Faker\Generator as Faker;

$factory->define(BidDocumentVerification::class, function (Faker $faker) {

    return [
        'attachment_id' => $faker->randomDigitNotNull,
        'bis_submission_master_id' => $faker->randomDigitNotNull,
        'document_submit_type' => $faker->word,
        'submit_remarks' => $faker->word,
        'verified_by' => $faker->randomDigitNotNull,
        'verified_date' => $faker->date('Y-m-d H:i:s'),
        'status' => $faker->word,
        'remarks' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
