<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AttachmentTypeConfiguration;
use Faker\Generator as Faker;

$factory->define(AttachmentTypeConfiguration::class, function (Faker $faker) {

    return [
        'document_attachment_id' => $faker->randomDigitNotNull,
        'attachment_type' => $faker->randomDigitNotNull,
        'created_by' => $faker->randomDigitNotNull,
        'updated_by' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
