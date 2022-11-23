<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSStagMenueSalesItemDetail;
use Faker\Generator as Faker;

$factory->define(POSStagMenueSalesItemDetail::class, function (Faker $faker) {

    return [
        'warehouseAutoID' => $faker->randomDigitNotNull,
        'menuSalesItemID' => $faker->randomDigitNotNull,
        'menuSalesID' => $faker->randomDigitNotNull,
        'itemAutoID' => $faker->randomDigitNotNull,
        'qty' => $faker->randomDigitNotNull,
        'UOM' => $faker->word,
        'UOMID' => $faker->randomDigitNotNull,
        'cost' => $faker->randomDigitNotNull,
        'actualInventoryCost' => $faker->randomDigitNotNull,
        'menuID' => $faker->randomDigitNotNull,
        'menuSalesQty' => $faker->randomDigitNotNull,
        'costGLAutoID' => $faker->randomDigitNotNull,
        'assetGLAutoID' => $faker->randomDigitNotNull,
        'isWastage' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'segmentID' => $faker->randomDigitNotNull,
        'segmentCode' => $faker->word,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'createdUserGroup' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timeStamp' => $faker->date('Y-m-d H:i:s'),
        'is_sync' => $faker->randomDigitNotNull,
        'id_store' => $faker->randomDigitNotNull,
        'transaction_log_id' => $faker->randomDigitNotNull
    ];
});
