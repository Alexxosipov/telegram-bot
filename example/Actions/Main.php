<?php

namespace Alexxosipov\TelegramBot\Example\Actions;

use Alexxosipov\TelegramBot\Actions\ActionHandler;
use Alexxosipov\TelegramBot\Example\Enums\Action;
use Alexxosipov\TelegramBot\Response\Keyboard;
use Alexxosipov\TelegramBot\Response\KeyboardButton;
use Alexxosipov\TelegramBot\Response\Response;

class Main extends ActionHandler
{
    public function buildPreviewMessage(array $data = []): Response
    {
        return new Response(
            text: "Hey {$this->telegramUser->first_name}, welcome to the bot!",
            keyboard: new Keyboard([
                [new KeyboardButton('Read the docs', Action::Docs)],
                [new KeyboardButton('Join the group', Action::Group)],
                [new KeyboardButton('Order bot development', Action::OrderBot)],
            ])
        );
    }
}