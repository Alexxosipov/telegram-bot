<?php

namespace Alexxosipov\TelegramBot\Actions\Contracts;

use Alexxosipov\TelegramBot\Response\Response;

interface HasVoiceMessage
{
    public function handleMessage(string $message): ?Response;
}