<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SMEDocumentCodes;
use Faker\Generator as Faker;

$factory->define(SMEDocumentCodes::class, function (Faker $faker) {

    return [
        'documentID' => $faker->word,
        'document' => $faker->word,
        'isApprovalDocument' => $faker->randomDigitNotNull,
        'isFinance' => $faker->randomDigitNotNull,
        'moduleID' => $faker->randomDigitNotNull,
        'icon' => $faker->word,
        'documentTable' => $faker->word
    ];
});
