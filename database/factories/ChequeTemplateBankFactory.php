<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ChequeTemplateBank;
use Faker\Generator as Faker;

$factory->define(ChequeTemplateBank::class, function (Faker $faker) {

    return [
        'cheque_template_master_id' => $faker->randomDigitNotNull,
        'bank_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
