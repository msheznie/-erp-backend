<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSSourceMenuSalesServiceCharge;
use Faker\Generator as Faker;

$factory->define(POSSourceMenuSalesServiceCharge::class, function (Faker $faker) {

    return [
        'wareHouseAutoID' => $faker->randomDigitNotNull,
        'menuSalesID' => $faker->randomDigitNotNull,
        'menuSalesItemID' => $faker->randomDigitNotNull,
        'menuServiceChargeID' => $faker->randomDigitNotNull,
        'menuMasterID' => $faker->randomDigitNotNull,
        'serviceChargePercentage' => $faker->randomDigitNotNull,
        'serviceChargeAmount' => $faker->randomDigitNotNull,
        'GLAutoID' => $faker->randomDigitNotNull,
        'beforeDiscountTotalServiceCharge' => $faker->randomDigitNotNull,
        'menusalesDiscount' => $faker->randomDigitNotNull,
        'menusalesPromotionalDiscount' => $faker->randomDigitNotNull,
        'unitMenuServiceCharge' => $faker->randomDigitNotNull,
        'menusalesItemQty' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'companyCode' => $faker->word,
        'createdUserGroup' => $faker->randomDigitNotNull,
        'createdPCID' => $faker->word,
        'createdUserID' => $faker->word,
        'createdDateTime' => $faker->date('Y-m-d H:i:s'),
        'createdUserName' => $faker->word,
        'modifiedPCID' => $faker->word,
        'modifiedUserID' => $faker->word,
        'modifiedDateTime' => $faker->date('Y-m-d H:i:s'),
        'modifiedUserName' => $faker->word,
        'timestamp' => $faker->date('Y-m-d H:i:s'),
        'is_sync' => $faker->randomDigitNotNull,
        'id_store' => $faker->randomDigitNotNull,
        'transaction_log_id' => $faker->randomDigitNotNull
    ];
});
