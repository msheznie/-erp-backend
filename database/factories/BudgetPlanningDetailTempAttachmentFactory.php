<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\BudgetPlanningDetailTempAttachment;
use Faker\Generator as Faker;

$factory->define(BudgetPlanningDetailTempAttachment::class, function (Faker $faker) {

    return [
        'entry_id' => $faker->randomDigitNotNull,
        'file_name' => $faker->word,
        'original_file_name' => $faker->word,
        'file_path' => $faker->word,
        'file_type' => $faker->word,
        'file_size' => $faker->word,
        'attachment_type_id' => $faker->randomDigitNotNull,
        'uploaded_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
