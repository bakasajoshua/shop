<?php

use Faker\Generator as Faker;

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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'user_type_id' => rand(1, 5),
        'telephone' => $faker->telephone,
        'email' => $faker->unique()->safeEmail,
        // 'password' => $password ?: $password = bcrypt('secret'),
        'password' => env('MASTER_PASSWORD', 12345678),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->paragraph,
    ];
});

$factory->define(App\Product::class, function (Faker $faker) {
    $images = [
        'item1.png','item2.png','item3.png','item4.png','item5.png','item6.png','item7.png','item8.png',
        'item9.png','item10.png','item11.png','item12.png','item13.png','item14.png','item15.png','item16.png'
    ];
    return [
        'name' => $faker->name,
        'price' => rand(100, 1000),
        'quantity' => rand(1, 10),
        'category_id' => rand(1, 6),
        'image' => env('APP_URL').'img/items/'.$images[array_rand($images, 1)],
    ];
});
