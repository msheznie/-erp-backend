<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ReportTemplateEquity;
use Faker\Generator as Faker;

$factory->define(ReportTemplateEquity::class, function (Faker $faker) {

    return [
        'templateMasterID' => $faker->randomDigitNotNull,
        'description' => $faker->word,
        'sort_order' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
