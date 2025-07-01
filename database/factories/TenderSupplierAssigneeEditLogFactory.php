<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderSupplierAssigneeEditLog;
use Faker\Generator as Faker;

$factory->define(TenderSupplierAssigneeEditLog::class, function (Faker $faker) {

    return [
        'company_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'id' => $faker->randomDigitNotNull,
        'is_deleted' => $faker->randomDigitNotNull,
        'level_no' => $faker->randomDigitNotNull,
        'mail_sent' => $faker->randomDigitNotNull,
        'registration_link_id' => $faker->randomDigitNotNull,
        'registration_number' => $faker->word,
        'supplier_assigned_id' => $faker->randomDigitNotNull,
        'supplier_email' => $faker->word,
        'supplier_name' => $faker->word,
        'tender_master_id' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'version_id' => $faker->randomDigitNotNull
    ];
});
