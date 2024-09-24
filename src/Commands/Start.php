<?php

namespace Alexxosipov\Telegram\Commands;

use Alexxosipov\Telegram\Actions\Action;
use Alexxosipov\Telegram\Response\Response;

class Start extends CommandHandler
{
    public function handle(): ?Response
    {
        return $this->sendNextActionPreview(Action::Welcome);
    }
}