<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ChequeUpdateReason;
use Faker\Generator as Faker;

$factory->define(ChequeUpdateReason::class, function (Faker $faker) {

    return [
        'cheque_register_detail_id' => $faker->randomDigitNotNull,
        'is_switch' => $faker->randomDigitNotNull,
        'update_switch_reason' => $faker->text,
        'current_cheque_id' => $faker->word,
        'previous_cheque_id' => $faker->word,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
