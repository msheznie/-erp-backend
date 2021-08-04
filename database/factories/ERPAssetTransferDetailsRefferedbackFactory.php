<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ERPAssetTransferDetailsRefferedback;
use Faker\Generator as Faker;

$factory->define(ERPAssetTransferDetailsRefferedback::class, function (Faker $faker) {

    return [
        'id' => $faker->randomDigitNotNull,
        'erp_fa_fa_asset_transfer_id' => $faker->randomDigitNotNull,
        'erp_fa_fa_asset_request_id' => $faker->randomDigitNotNull,
        'erp_fa_fa_asset_request_detail_id' => $faker->randomDigitNotNull,
        'from_location_id' => $faker->randomDigitNotNull,
        'to_location_id' => $faker->randomDigitNotNull,
        'receivedYN' => $faker->randomDigitNotNull,
        'fa_master_id' => $faker->randomDigitNotNull,
        'pr_created_yn' => $faker->randomDigitNotNull,
        'timesReferred' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'created_user_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
