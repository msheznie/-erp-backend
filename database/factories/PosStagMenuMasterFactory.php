<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PosStagMenuMaster;
use Faker\Generator as Faker;

$factory->define(PosStagMenuMaster::class, function (Faker $faker) {

    return [
        'menuMasterDescription' => $faker->word,
        'menuImage' => $faker->word,
        'menuCategoryID' => $faker->randomDigitNotNull,
        'menuCost' => $faker->randomDigitNotNull,
        'barcode' => $faker->word,
        'sellingPrice' => $faker->randomDigitNotNull,
        'pricewithoutTax' => $faker->randomDigitNotNull,
        'revenueGLAutoID' => $faker->randomDigitNotNull,
        'TAXpercentage' => $faker->randomDigitNotNull,
        'totalTaxAmount' => $faker->randomDigitNotNull,
        'taxMasterID' => $faker->randomDigitNotNull,
        'totalServiceCharge' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'menuStatus' => $faker->randomDigitNotNull,
        'kotID' => $faker->randomDigitNotNull,
        'preparationTime' => $faker->randomDigitNotNull,
        'isPass' => $faker->randomDigitNotNull,
        'isPack' => $faker->randomDigitNotNull,
        'isVeg' => $faker->randomDigitNotNull,
        'isAddOn' => $faker->randomDigitNotNull,
        'showImageYN' => $faker->randomDigitNotNull,
        'menuSizeID' => $faker->randomDigitNotNull,
        'sortOrder' => $faker->randomDigitNotNull,
        'sortOder' => $faker->randomDigitNotNull,
        'isDeleted' => $faker->randomDigitNotNull,
        'deletedBy' => $faker->randomDigitNotNull,
        'deletedDatetime' => $faker->date('Y-m-d H:i:s'),
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
        'transaction_log_id' => $faker->randomDigitNotNull
    ];
});
