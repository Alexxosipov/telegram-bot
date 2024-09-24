<?php

namespace Alexxosipov\Telegram\Traits;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Update;

trait HandlesCommand
{
    private function handleCommand(Update $update): void
    {
        $parsedCommand = explode(' ', $update->message->text);
        $command = $parsedCommand[0];
        $params = $parsedCommand[1] ?? null;
        $debug = compact('command', 'params');
        $debug['rawCommand'] = $update->message->text;

        Log::debug('Command', $debug);

        $commandHandler = $this->commandHandlerFactory->create(
            $command,
            $this->telegramUser,
            $this,
            $params
        );

        if ($commandHandler) {
            $this->sendResponse($commandHandler->handle());
        }
    }
}