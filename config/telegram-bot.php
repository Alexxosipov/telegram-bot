<?php

use Alexxosipov\Telegram\Actions\Action;
use Alexxosipov\Telegram\Actions\Welcome;
use Alexxosipov\Telegram\Commands\Start;
use Alexxosipov\Telegram\Storage\DatabaseStorage;

return [
    'bot-token' => env('TELEGRAM_BOT_TOKEN'),
    'action-enum' => Action::class,
    'default-action' => Action::Welcome,
    'storage' => DatabaseStorage::class,
    'actions' => [
        Action::Welcome->value => Welcome::class
    ],
    'commands' => [
        '/start' => Start::class,
    ],

    'webhook-endpoint' => '/api/telegram/webhook',
];