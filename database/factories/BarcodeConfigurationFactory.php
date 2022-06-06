<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BarcodeConfiguration;
use Faker\Generator as Faker;

$factory->define(BarcodeConfiguration::class, function (Faker $faker) {

    return [
        'barcode_font' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'height' => $faker->word,
        'no_of_coulmns' => $faker->word,
        'no_of_rows' => $faker->word,
        'page_size' => $faker->word,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'width' => $faker->word
    ];
});
