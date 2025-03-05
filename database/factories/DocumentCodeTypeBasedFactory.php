<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentCodeTypeBased;
use Faker\Generator as Faker;

$factory->define(DocumentCodeTypeBased::class, function (Faker $faker) {

    return [
        'document_transaction_id' => $faker->randomDigitNotNull,
        'type_name' => $faker->word,
        'master_prefix' => $faker->word,
        'type_prefix' => $faker->word,
        'is_active' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
