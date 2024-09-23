<?php

namespace Alexxosipov\Telegram;

use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/telegram-bot.php' => config_path('telegram-bot.php')
            ]);

            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ]);
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/telegram-bot.php', 'telegram-bot');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}