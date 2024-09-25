<?php

namespace Alexxosipov\TelegramBot;

use Alexxosipov\TelegramBot\Artisan\SetWebhook;
use Alexxosipov\TelegramBot\Response\Sender\ResponseSender;
use Alexxosipov\TelegramBot\Response\Sender\ResponseSenderContract;
use Alexxosipov\TelegramBot\Storage\DatabaseStorage;
use Alexxosipov\TelegramBot\Storage\StorageContract;
use Illuminate\Support\ServiceProvider;

class TelegramBotServiceProvider extends ServiceProvider
{
    public $bindings = [
        ResponseSenderContract::class => ResponseSender::class,
        StorageContract::class => DatabaseStorage::class
    ];

    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/telegram-bot.php' => config_path('telegram-bot.php')
            ]);

            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ]);

            $this->commands([
                SetWebhook::class
            ]);
        }
    }
}