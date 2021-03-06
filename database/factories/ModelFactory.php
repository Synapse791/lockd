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

$factory->define(Lockd\Models\User::class, function (Faker\Generator $faker) {
    return [
        'firstName' => $faker->firstName,
        'lastName' => $faker->lastName,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(\Lockd\Models\Group::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
    ];
});

$factory->define(\Lockd\Models\Folder::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'parent_id' => 0,
    ];
});

$factory->define(\Lockd\Models\Password::class, function (Faker\Generator $faker) {
    return [
        'folder_id' => 0,
        'name' => $faker->firstName,
        'url' => $faker->url,
        'user' => $faker->userName,
        'password' => Crypt::encrypt('letmein'),
    ];
});
