<?php

declare(strict_types=1);

namespace Alexxosipov\TelegramBot\Keyboards;

use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Response\Keyboard;

abstract class BaseKeyboard
{
    final public function __construct(
        protected TelegramUser $telegramUser
    ) {
    }

    public static function make(TelegramUser $telegramUser): self
    {
        $class = new static($telegramUser);
        $class->prepare();

        return $class;
    }

    abstract public function build(): Keyboard;

    protected function prepare(): void
    {

    }
}
