<?php

declare(strict_types=1);

namespace Alexxosipov\Telegram\Commands;

use Alexxosipov\Telegram\Models\TelegramUser;
use Alexxosipov\Telegram\TelegramBot;

class CommandHandlerFactory
{
    public function create(
        string         $command,
        TelegramUser   $telegramUser,
        TelegramBot $telegramBot,
        ?string        $data = null
    ): ?CommandHandler {
        $class = config('telegram-bot.commands')[$command] ?? null;

        if ( ! $class) {
            return null;
        }

        return new $class($telegramUser, $telegramBot, $data);
    }
}
