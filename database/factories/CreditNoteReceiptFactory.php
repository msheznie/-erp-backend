<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CreditNoteReceipt;
use Faker\Generator as Faker;

$factory->define(CreditNoteReceipt::class, function (Faker $faker) {

    return [
        'creditNoteAutoID' => $faker->randomDigitNotNull,
        'custReceivePaymentAutoID' => $faker->randomDigitNotNull,
        'refundAmount' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
