<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\HrDeligationDetails;
use Faker\Generator as Faker;

$factory->define(HrDeligationDetails::class, function (Faker $faker) {

    return [
        'approval_level' => $faker->randomDigitNotNull,
        'approval_role' => $faker->randomDigitNotNull,
        'approval_user_id' => $faker->randomDigitNotNull,
        'comment' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'delegatee_id' => $faker->randomDigitNotNull,
        'delegation_id' => $faker->randomDigitNotNull,
        'document_id' => $faker->word,
        'enabled' => $faker->word,
        'module_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
