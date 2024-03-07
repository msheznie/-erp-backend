<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PaymentTermConfig;
use Faker\Generator as Faker;

$factory->define(PaymentTermConfig::class, function (Faker $faker) {

    return [
        'templateId' => $faker->word,
        'term' => $faker->word,
        'description' => $faker->text,
        'sortOrder' => $faker->randomDigitNotNull,
        'isSelected' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
