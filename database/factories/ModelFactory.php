<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/


$factory->define(\App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});



//$factory->define(App\Model\Merchant::class, function (Faker\Generator $faker) {
//    return [
//        'name' => $faker->name,
//        'email' => $faker->unique()->safeEmail,
//        'password' => bcrypt('secret'),
//        'remember_token' => str_random(10),
//    ];
//});

$factory->define(App\Model\Role::class, function (Faker\Generator $faker) {
    return [];
});

$factory->define(\App\Model\Lookup::class, function (Faker\Generator $faker) {
    return [
        'type_id_field' => str_random(10),
        'type_id_value' => str_random(10),
        'type_id_name' => str_random(10),
    ];
});