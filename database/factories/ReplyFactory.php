<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Reply;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Reply::class, function (Faker $faker) {
    $date_time = $faker->dateTimeThisMonth();
    return [
        'content'=>$faker->sentence(),
        'created_at' => $date_time,
        'updated_at' => $date_time,
    ];
});
