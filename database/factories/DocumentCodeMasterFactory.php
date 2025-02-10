<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentCodeMaster;
use Faker\Generator as Faker;

$factory->define(DocumentCodeMaster::class, function (Faker $faker) {

    return [
        'module_id' => $faker->randomDigitNotNull,
        'document_transaction_id' => $faker->randomDigitNotNull,
        'numbering_sequence_id' => $faker->randomDigitNotNull,
        'last_serial' => $faker->randomDigitNotNull,
        'isCommonSerialization' => $faker->randomDigitNotNull,
        'isTypeBasedSerialization' => $faker->randomDigitNotNull,
        'formatCount' => $faker->randomDigitNotNull,
        'serial_length' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
