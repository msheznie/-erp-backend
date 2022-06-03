<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderDocumentTypes;
use Faker\Generator as Faker;

$factory->define(TenderDocumentTypes::class, function (Faker $faker) {

    return [
        'document_type' => $faker->word,
        'srm_action' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull
    ];
});
