<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DocumentCodeTransaction;
use Faker\Generator as Faker;

$factory->define(DocumentCodeTransaction::class, function (Faker $faker) {

    return [
        'module_id' => $faker->randomDigitNotNull,
        'transaction_name' => $faker->word,
        'master_prefix' => $faker->word,
        'is_active' => $faker->randomDigitNotNull,
        'isGettingEdited' => $faker->randomDigitNotNull,
        'isTypeEnable' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
