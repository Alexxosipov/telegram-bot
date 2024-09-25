<?php

namespace Alexxosipov\TelegramBot\Example\Actions;

use Alexxosipov\TelegramBot\Actions\ActionHandler;
use Alexxosipov\TelegramBot\Example\Enums\Action;
use Alexxosipov\TelegramBot\Response\Keyboard;
use Alexxosipov\TelegramBot\Response\KeyboardButton;
use Alexxosipov\TelegramBot\Response\Response;

class Group extends ActionHandler
{
    public function buildPreviewMessage(array $data = []): Response
    {
        return new Response(
            text: 'You are welcome to join our group via link:',
            keyboard: new Keyboard([
                [new KeyboardButton('Join the group', url: 'https://t.me/alexxoispov')],
                [new KeyboardButton('Back', Action::Main)],
            ])
        );
    }
}