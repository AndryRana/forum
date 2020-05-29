<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Channel;
use App\Reply;
use App\Thread;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\NotificationSender;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

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

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

$factory->define(Thread::class, function (Faker $faker) {
    return [
        'user_id' => factory(App\User::class),
        'channel_id' => factory(App\Channel::class),
        'title' => $faker->sentence,
        'body' => $faker->paragraph
    ];
});

$factory->define(Channel::class, function (Faker $faker) {
   $name = $faker->word;
    return [
        'name' => $name,
        'slug' => $name
    ];
});

$factory->define(Reply::class, function (Faker $faker) {
    return [
        'thread_id' => factory(App\Thread::class),
        'user_id' => factory(App\User::class),
        'body' => $faker->paragraph
    ];
});

$factory->define(DatabaseNotification::class, function (Faker $faker) {
    return [
        'id' => Uuid::uuid4()->toString(),
        'type' => 'App\Notifications\ThreadWasUpdated',
        'notifiable_id' => function () {
            return auth()->id() ?: factory('App\User')->create()->id;
        },
        'notifiable_type' => 'App\User',
        'data' => ['foo' => 'bar']
    ];
});


