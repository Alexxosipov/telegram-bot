<?php

namespace Alexxosipov\Telegram\Actions\Contracts;

use Alexxosipov\Telegram\Response\Response;

interface HasCallbackQuery
{
    public function handleCallbackQuery(array $data): ?Response;
}