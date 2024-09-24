<?php

declare(strict_types=1);

namespace Alexxosipov\Telegram\Keyboards;

use Alexxosipov\Telegram\Models\TelegramUser;
use Alexxosipov\Telegram\Response\Keyboard as ResponseKeyboard;

abstract class Keyboard
{
    public function __construct(
        protected TelegramUser $telegramUser
    ) {
    }

    public static function make(TelegramUser $telegramUser): static
    {
        return new static($telegramUser);
    }

    abstract public function build(): Keyboard;
}
