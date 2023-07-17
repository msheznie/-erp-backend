<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AssetWarranty;
use Faker\Generator as Faker;

$factory->define(AssetWarranty::class, function (Faker $faker) {

    return [
        'documentSystemCode' => $faker->randomDigitNotNull,
        'warranty_provider' => $faker->word,
        'start_date' => $faker->word,
        'duration' => $faker->randomDigitNotNull,
        'end_date' => $faker->word,
        'warranty_coverage' => $faker->text,
        'claim_process' => $faker->text,
        'extended_warranty' => $faker->text,
        'createdUserID' => $faker->word,
        'createdUserSystemID' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_at' => $faker->date('Y-m-d H:i:s')
    ];
});
