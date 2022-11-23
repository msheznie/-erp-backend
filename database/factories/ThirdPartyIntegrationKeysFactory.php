<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ThirdPartyIntegrationKeys;
use Faker\Generator as Faker;

$factory->define(ThirdPartyIntegrationKeys::class, function (Faker $faker) {

    return [
        'company_id' => $faker->randomDigitNotNull,
        'third_party_system_id' => $faker->randomDigitNotNull,
        'api_key' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
