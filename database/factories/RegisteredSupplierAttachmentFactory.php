<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RegisteredSupplierAttachment;
use Faker\Generator as Faker;

$factory->define(RegisteredSupplierAttachment::class, function (Faker $faker) {

    return [
        'resgisteredSupplierID' => $faker->randomDigitNotNull,
        'attachmentDescription' => $faker->word,
        'originalFileName' => $faker->word,
        'myFileName' => $faker->word,
        'sizeInKbs' => $faker->randomDigitNotNull,
        'path' => $faker->word,
        'isUploaded' => $faker->randomDigitNotNull
    ];
});
