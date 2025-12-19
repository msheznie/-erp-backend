<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderBidClarifications;
use Faker\Generator as Faker;

$factory->define(TenderBidClarifications::class, function (Faker $faker) {

    return [
        'comment' => $faker->text,
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'is_answered' => $faker->randomDigitNotNull,
        'is_public' => $faker->randomDigitNotNull,
        'parent_id' => $faker->randomDigitNotNull,
        'post' => $faker->text,
        'supplier_id' => $faker->randomDigitNotNull,
        'tender_master_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'user_id' => $faker->randomDigitNotNull
    ];
});
