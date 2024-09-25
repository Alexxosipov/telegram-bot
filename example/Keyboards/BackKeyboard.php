<?php

namespace Alexxosipov\TelegramBot\Example\Keyboards;

use Alexxosipov\TelegramBot\Example\Enums\Action;
use Alexxosipov\TelegramBot\Keyboards\BaseKeyboard;
use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Response\Keyboard;
use Alexxosipov\TelegramBot\Response\KeyboardButton;

class BackKeyboard extends BaseKeyboard
{
    private \BackedEnum $action;

    public function __construct(TelegramUser $telegramUser)
    {
        $this->withAction(Action::Main);

        parent::__construct($telegramUser);
    }

    public function build(): Keyboard
    {
        return new Keyboard([
            [new KeyboardButton('Back', $this->action)],
        ]);
    }

    public function withAction(\BackedEnum $action): BackKeyboard
    {
        $this->action = $action;

        return $this;
    }
}