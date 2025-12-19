<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AppointmentDetails;
use Faker\Generator as Faker;

$factory->define(AppointmentDetails::class, function (Faker $faker) {

    return [
        'appointment_id' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'created_by' => $faker->randomDigitNotNull,
        'item_id' => $faker->randomDigitNotNull,
        'po_master_id' => $faker->randomDigitNotNull,
        'qty' => $faker->randomDigitNotNull,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
