<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AssetRequestDetail;
use Faker\Generator as Faker;

$factory->define(AssetRequestDetail::class, function (Faker $faker) {

    return [
        'erp_fa_fa_asset_request_id' => $faker->randomDigitNotNull,
        'detail' => $faker->word,
        'qty' => $faker->randomDigitNotNull,
        'comment' => $faker->text,
        'company_id' => $faker->randomDigitNotNull,
        'created_user_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
