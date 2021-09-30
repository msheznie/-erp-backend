<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SrpErpTemplateMaster;
use Faker\Generator as Faker;

$factory->define(SrpErpTemplateMaster::class, function (Faker $faker) {

    return [
        'TempDes' => $faker->word,
        'TempPageName' => $faker->word,
        'TempPageNameLink' => $faker->word,
        'createPageLink' => $faker->word,
        'FormCatID' => $faker->randomDigitNotNull,
        'isReport' => $faker->randomDigitNotNull,
        'isDefault' => $faker->randomDigitNotNull,
        'documentCode' => $faker->word,
        'templateKey' => $faker->word
    ];
});
