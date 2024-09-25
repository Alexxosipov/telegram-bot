<?php

namespace Alexxosipov\TelegramBot\Example\Actions;

use Alexxosipov\TelegramBot\Actions\ActionHandler;
use Alexxosipov\TelegramBot\Example\Keyboards\BackKeyboard;
use Alexxosipov\TelegramBot\Response\Response;

class OrderBot extends ActionHandler
{
    public function buildPreviewMessage(array $data = []): Response
    {
        $userName = $data['username'] ?? '@alexxosipov';
        return new Response(
            text: "To order bot please text $userName",
            keyboard: $this->buildKeyboard(BackKeyboard::class)
        );
    }
}