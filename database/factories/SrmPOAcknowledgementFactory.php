<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SrmPOAcknowledgement;
use Faker\Generator as Faker;

$factory->define(SrmPOAcknowledgement::class, function (Faker $faker) {

    return [
        'comment' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'po_id' => $faker->randomDigitNotNull,
        'supplier_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull
    ];
});
