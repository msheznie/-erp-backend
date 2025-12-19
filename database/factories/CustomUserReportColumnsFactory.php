<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CustomUserReportColumns;
use Faker\Generator as Faker;

$factory->define(CustomUserReportColumns::class, function (Faker $faker) {

    return [
        'user_report_id' => $faker->randomDigitNotNull,
        'column_id' => $faker->randomDigitNotNull,
        'label' => $faker->word,
        'is_sortable' => $faker->word,
        'sort_by' => $faker->word,
        'is_group_by' => $faker->word,
        'is_filter' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
