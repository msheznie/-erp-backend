<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SRMDocumentMaster;
use Faker\Generator as Faker;

$factory->define(SRMDocumentMaster::class, function (Faker $faker) {

    return [
        'document_name' => $faker->word,
        'document_area' => $faker->word,
        'envelope_type' => $faker->word,
        'default_to_tender' => $faker->word,
        'default_to_rfx' => $faker->word,
        'path' => $faker->word,
        'original_file_name' => $faker->word,
        'size_in_kbs' => $faker->word,
        'company_system_id' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
