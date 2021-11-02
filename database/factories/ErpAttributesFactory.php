<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ErpAttributes;
use Faker\Generator as Faker;

$factory->define(ErpAttributes::class, function (Faker $faker) {

    return [
        'description' => $faker->word,
        'field_type' => $faker->randomDigitNotNull,
        'document_id' => $faker->randomDigitNotNull,
        'document_master_id' => $faker->randomDigitNotNull,
        'is_mendatory' => $faker->word,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
