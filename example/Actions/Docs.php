<?php

namespace Alexxosipov\TelegramBot\Example\Actions;

use Alexxosipov\TelegramBot\Actions\ActionHandler;
use Alexxosipov\TelegramBot\Example\Keyboards\BackKeyboard;
use Alexxosipov\TelegramBot\Response\Response;

class Docs extends ActionHandler
{
    public function buildPreviewMessage(array $data = []): Response
    {
        return new Response(
            text: 'Here is the docs.',
            keyboard: $this->buildKeyboard(BackKeyboard::class)
        );
    }
}