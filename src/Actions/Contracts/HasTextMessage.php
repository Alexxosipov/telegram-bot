<?php

namespace Alexxosipov\TelegramBot\Actions\Contracts;

use Alexxosipov\TelegramBot\Response\Response;

interface HasTextMessage
{
    public function handleMessage(string $message): ?Response;
}