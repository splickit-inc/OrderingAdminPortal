<?php

$factory->define(App\Model\Organization::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->randomElement(['Splickit', 'Sodexo']),
        'description' => 'This is the organization description.',
    ];
});

$factory->defineAs(App\Model\Organization::class, 'Splickit', function (Faker\Generator $faker) {
    return [
        "id" => 1,
        "name" => "Splickit",
        "description" => "This is the Splickit organizations",
    ];
});

$factory->defineAs(App\Model\Organization::class, 'Sodexo', function (Faker\Generator $faker) {
    return [
        "id" => "2",
        "name" => "Sodexo",
        "description" => "Sodexo - Quality of Life Services"
    ];
});
