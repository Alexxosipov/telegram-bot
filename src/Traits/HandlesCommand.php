<?php

namespace Alexxosipov\TelegramBot\Traits;

use Alexxosipov\TelegramBot\Response\Response;
use Telegram\Bot\Objects\Update;

trait HandlesCommand
{
    private function handleCommand(Update $update): ?Response
    {
        $parsedCommand = explode(' ', $update->message->text);
        $command = $parsedCommand[0];
        $params = $parsedCommand[1] ?? null;

        $commandHandler = $this->commandHandlerFactory->create(
            $command,
            $this->telegramUser,
            $this,
            $params
        );

        return $commandHandler?->handle();
    }
}