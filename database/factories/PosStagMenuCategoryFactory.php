<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PosStagMenuCategory;
use Faker\Generator as Faker;

$factory->define(PosStagMenuCategory::class, function (Faker $faker) {

    return [
        'menuCategoryDescription' => $faker->word,
        'image' => $faker->word,
        'revenueGLAutoID' => $faker->randomDigitNotNull,
        'topSalesRptYN' => $faker->randomDigitNotNull,
        'companyID' => $faker->randomDigitNotNull,
        'sortOrder' => $faker->randomDigitNotNull,
        'isPack' => $faker->randomDigitNotNull,
        'masterLevelID' => $faker->randomDigitNotNull,
        'levelNo' => $faker->randomDigitNotNull,
        'bgColor' => $faker->word,
        'isActive' => $faker->randomDigitNotNull,
        'showImageYN' => $faker->randomDigitNotNull,
        'isDeleted' => $faker->randomDigitNotNull,
        'deletedBy' => $faker->word,
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
