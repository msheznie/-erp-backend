<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TemplateSectionTableRow;
use Faker\Generator as Faker;

$factory->define(TemplateSectionTableRow::class, function (Faker $faker) {

    return [
        'table_id' => $faker->randomDigitNotNull,
        'row_data' => $faker->text,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
