<?php

namespace Alexxosipov\TelegramBot\Actions;

use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Storage\StorageContract;
use Alexxosipov\TelegramBot\TelegramBot;

class ActionHandlerFactory
{
    public function create(TelegramUser $telegramUser, TelegramBot $telegramBot): ?ActionHandler
    {
        $handlerClass = config('telegram-bot.actions')[$telegramUser->action->value] ?? null;

        if (!$handlerClass) {
            return null;
        }

        $storage = app()->makeWith(StorageContract::class, [
            'user' => $telegramUser
        ]);

        return new $handlerClass($telegramUser, $telegramBot, $storage);
    }
}