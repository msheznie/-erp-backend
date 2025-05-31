<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ReportCustomColumn;
use Faker\Generator as Faker;

$factory->define(ReportCustomColumn::class, function (Faker $faker) {

    return [
        'column_name' => $faker->word,
        'column_reference' => $faker->text,
        'column_slug' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'isActive' => $faker->word,
        'isDefault' => $faker->word,
        'master_column_reference' => $faker->text,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
