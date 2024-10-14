<?php

namespace Alexxosipov\TelegramBot\Actions\Contracts;

use BackedEnum;
use Alexxosipov\TelegramBot\Response\Response;

interface HasCallbackQuery
{
    public function handleCallbackQuery(BackedEnum $action, array $data): ?Response;
}