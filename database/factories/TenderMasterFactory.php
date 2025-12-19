<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TenderMaster;
use Faker\Generator as Faker;

$factory->define(TenderMaster::class, function (Faker $faker) {

    return [
        'title' => $faker->word,
        'title_sec_lang' => $faker->word,
        'description' => $faker->word,
        'description_sec_lang' => $faker->word,
        'tender_type_id' => $faker->randomDigitNotNull,
        'currency_id' => $faker->randomDigitNotNull,
        'envelop_type_id' => $faker->randomDigitNotNull,
        'procument_cat_id' => $faker->randomDigitNotNull,
        'procument_sub_cat_id' => $faker->randomDigitNotNull,
        'evaluation_type_id' => $faker->randomDigitNotNull,
        'estimated_value' => $faker->randomDigitNotNull,
        'allocated_budget' => $faker->randomDigitNotNull,
        'budget_document' => $faker->text,
        'tender_document_fee' => $faker->randomDigitNotNull,
        'bank_id' => $faker->randomDigitNotNull,
        'bank_account_id' => $faker->randomDigitNotNull,
        'document_sales_start_date' => $faker->date('Y-m-d H:i:s'),
        'document_sales_end_date' => $faker->date('Y-m-d H:i:s'),
        'pre_bid_clarification_start_date' => $faker->date('Y-m-d H:i:s'),
        'pre_bid_clarification_end_date' => $faker->date('Y-m-d H:i:s'),
        'pre_bid_clarification_method' => $faker->randomDigitNotNull,
        'site_visit_date' => $faker->date('Y-m-d H:i:s'),
        'site_visit_end_date' => $faker->date('Y-m-d H:i:s'),
        'bid_submission_opening_date' => $faker->date('Y-m-d H:i:s'),
        'bid_submission_closing_date' => $faker->date('Y-m-d H:i:s'),
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'updated_by' => $faker->randomDigitNotNull,
        'deleted_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_by' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull
    ];
});
