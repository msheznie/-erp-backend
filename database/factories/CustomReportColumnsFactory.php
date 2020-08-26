<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomReportColumns;
use Faker\Generator as Faker;

$factory->define(CustomReportColumns::class, function (Faker $faker) {

    return [
        'report_master_id' => $faker->randomDigitNotNull,
        'label' => $faker->word,
        'column' => $faker->word,
        'column_type' => $faker->randomDigitNotNull,
        'sort_order' => $faker->randomDigitNotNull,
        'is_sortabel' => $faker->word,
        'sort_by' => $faker->word,
        'is_group_by' => $faker->word,
        'is_default_sort' => $faker->word,
        'is_default_group_by' => $faker->word,
        'is_filter' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
