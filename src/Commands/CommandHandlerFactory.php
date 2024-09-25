<?php

declare(strict_types=1);

namespace Alexxosipov\TelegramBot\Commands;

use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Storage\BaseStorage;
use Alexxosipov\TelegramBot\Storage\StorageContract;
use Alexxosipov\TelegramBot\TelegramBot;

class CommandHandlerFactory
{
    public function create(
        string       $command,
        TelegramUser $telegramUser,
        TelegramBot  $telegramBot,
        ?string      $data = null
    ): ?CommandHandler {
        $class = config('telegram-bot.commands')[$command] ?? null;

        if ( ! $class) {
            return null;
        }

        $storage = app()->makeWith(StorageContract::class, [
            'user' => $telegramUser
        ]);

        return new $class($telegramUser, $telegramBot, $storage, $data);
    }
}
