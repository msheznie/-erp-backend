<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MolContribution;
use Faker\Generator as Faker;

$factory->define(MolContribution::class, function (Faker $faker) {

    return [
        'authority_id' => $faker->randomDigitNotNull,
        'company_id' => $faker->randomDigitNotNull,
        'contribution_type' => $faker->randomDigitNotNull,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'description' => $faker->word,
        'mol_calculation_type_id' => $faker->randomDigitNotNull,
        'mol_expense_gl_account_id' => $faker->randomDigitNotNull,
        'mol_percentage' => $faker->word,
        'status' => $faker->word,
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
