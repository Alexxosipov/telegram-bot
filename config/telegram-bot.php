<?php

use Alexxosipov\TelegramBot\Example\Actions\Docs;
use Alexxosipov\TelegramBot\Example\Actions\Group;
use Alexxosipov\TelegramBot\Example\Actions\OrderBot;
use Alexxosipov\TelegramBot\Example\Actions\Main;
use Alexxosipov\TelegramBot\Example\Commands\Start;
use Alexxosipov\TelegramBot\Example\Enums\Action;
use Alexxosipov\TelegramBot\Storage\DatabaseStorage;

return [
    'bot-token' => env('TELEGRAM_BOT_TOKEN'),
    'action-enum' => Action::class,
    'default-action' => Action::Main,
    'storage' => DatabaseStorage::class,
    'actions' => [
        Action::Main->value => Main::class,
        Action::Docs->value => Docs::class,
        Action::Group->value => Group::class,
        Action::OrderBot->value => OrderBot::class
    ],
    'commands' => [
        '/start' => Start::class,
    ],

    'webhook-endpoint' => '/api/telegram/webhook',
    'secret' => env('TELEGRAM_SECRET', 'test'),
];