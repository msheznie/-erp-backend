<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\POSSOURCEMenuSalesTaxes;
use Faker\Generator as Faker;

$factory->define(POSSOURCEMenuSalesTaxes::class, function (Faker $faker) {

    return [
        'wareHouseAutoID' => $faker->randomDigitNotNull,
        'menuSalesID' => $faker->randomDigitNotNull,
        'menuSalesItemID' => $faker->randomDigitNotNull,
        'menuID' => $faker->randomDigitNotNull,
        'menutaxID' => $faker->randomDigitNotNull,
        'taxmasterID' => $faker->randomDigitNotNull,
        'vatType' => $faker->randomDigitNotNull,
        'GLCode' => $faker->randomDigitNotNull,
        'taxPercentage' => $faker->randomDigitNotNull,
        'taxAmount' => $faker->randomDigitNotNull,
        'beforeDiscountTotalTaxAmount' => $faker->randomDigitNotNull,
        'menusalesDiscount' => $faker->randomDigitNotNull,
        'menusalesPromotionalDiscount' => $faker->randomDigitNotNull,
        'unitMenuTaxAmount' => $faker->randomDigitNotNull,
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
        'transaction_log_id' => $faker->randomDigitNotNull,
        'isSync' => $faker->randomDigitNotNull
    ];
});
