<?php

namespace Alexxosipov\TelegramBotBot\Actions\Contracts;

use Alexxosipov\TelegramBot\Example\Enums\Action;
use Alexxosipov\TelegramBot\Response\Response;

interface HasCallbackQuery
{
    public function handleCallbackQuery(Action $action, array $data): ?Response;
}