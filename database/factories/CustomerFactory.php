<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'company_id' => 1,
        'display_name' => $faker->company,
        'contact_name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'currency_id' => 1,
        'vat_number' => '11111111',
    ];
});

$factory->afterCreating(Customer::class, function ($customer, $faker) {
    $customer->address('billing', [
        'name' => $faker->company,
        'address_1' => $faker->address,
        'state' => $faker->state,
        'city' => $faker->city,
        'zip' => $faker->postcode,
        'country_id' => 1,
    ]);

    $customer->address('shipping', [
        'name' => $faker->company,
        'address_1' => $faker->address,
        'state' => $faker->state,
        'city' => $faker->city,
        'zip' => $faker->postcode,
        'country_id' => 1,
    ]);
});
