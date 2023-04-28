<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentModifyRequestDetail;
use Faker\Generator as Faker;

$factory->define(DocumentModifyRequestDetail::class, function (Faker $faker) {

    return [
        'attribute' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'new_value' => $faker->word,
        'old_value' => $faker->word,
        'tender_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'version_id' => $faker->randomDigitNotNull
    ];
});
