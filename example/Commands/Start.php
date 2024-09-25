<?php

namespace Alexxosipov\TelegramBot\Example\Commands;

use Alexxosipov\TelegramBot\Commands\CommandHandler;
use Alexxosipov\TelegramBot\Example\Enums\Action;
use Alexxosipov\TelegramBot\Response\Response;

class Start extends CommandHandler
{
    public function handle(): ?Response
    {
        return $this->sendNextActionPreview(Action::Main);
    }
}