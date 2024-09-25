<?php

namespace Tests;

use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\TelegramBotServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase;
use Tests\Traits\FakeTelegramApi;
use Tests\Traits\HasResponseAsserts;

abstract class TelegramTestCase extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use HasResponseAsserts;
    use FakeTelegramApi;

    protected function getPackageProviders($app): array
    {
        return [
            TelegramBotServiceProvider::class
        ];
    }

    protected function createTelegramUser(
        ?\BackedEnum $action = null,
    )
    {
        return TelegramUser::create([
            'id' => rand(100000,100000000),
            'action' => $action->value ?? config('telegram-bot.default-action'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'username' => $this->faker->userName,
        ]);
    }
}