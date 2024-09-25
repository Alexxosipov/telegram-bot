<?php

declare(strict_types=1);

namespace Alexxosipov\TelegramBot\Keyboards;

use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Response\Keyboard;

abstract class BaseKeyboard
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
