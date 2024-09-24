<?php

namespace Alexxosipov\Telegram\Actions\Contracts;

use Alexxosipov\Telegram\Response\Response;

interface HasTextMessage
{
    public function handleMessage(string $message): ?Response;
}