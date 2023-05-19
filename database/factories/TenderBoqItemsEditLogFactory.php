<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderBoqItemsEditLog;
use Faker\Generator as Faker;

$factory->define(TenderBoqItemsEditLog::class, function (Faker $faker) {

    return [
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'description' => $faker->word,
        'item_name' => $faker->word,
        'main_work_id' => $faker->randomDigitNotNull,
        'master_id' => $faker->randomDigitNotNull,
        'modify_type' => $faker->randomDigitNotNull,
        'qty' => $faker->randomDigitNotNull,
        'tender_edit_version_id' => $faker->randomDigitNotNull,
        'tender_id' => $faker->randomDigitNotNull,
        'tender_ranking_line_item' => $faker->randomDigitNotNull,
        'uom' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
