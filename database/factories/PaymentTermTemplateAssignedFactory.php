<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PaymentTermTemplateAssigned;
use Faker\Generator as Faker;

$factory->define(PaymentTermTemplateAssigned::class, function (Faker $faker) {

    return [
        'templateId' => $faker->word,
        'company' => $faker->word,
        'supplierCategory' => $faker->word,
        'supplierId' => $faker->word,
        'supplierName' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
