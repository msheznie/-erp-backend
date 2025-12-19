<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SRMPublicLink;
use Faker\Generator as Faker;

$factory->define(SRMPublicLink::class, function (Faker $faker) {

    return [
        'link' => $faker->word,
        'api_key' => $faker->word,
        'expire_date' => $faker->word,
        'expired' => $faker->randomDigitNotNull,
        'current' => $faker->randomDigitNotNull,
        'company_id' => $faker->word,
        'created_user_group' => $faker->randomDigitNotNull,
        'created_pc_id' => $faker->word,
        'created_user_id' => $faker->word,
        'created_date_time' => $faker->date('Y-m-d H:i:s'),
        'created_user_name' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
