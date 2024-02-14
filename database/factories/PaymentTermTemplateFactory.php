<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PaymentTermTemplate;
use Faker\Generator as Faker;

$factory->define(PaymentTermTemplate::class, function (Faker $faker) {

    return [
        'templateName' => $faker->word,
        'description' => $faker->word,
        'is_default' => $faker->word,
        'is_active' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
