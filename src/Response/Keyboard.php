<?php

declare(strict_types=1);

namespace Alexxosipov\Telegram\Response;

use Telegram\Bot\Keyboard\Keyboard as TelegramKeyboard;

readonly class Keyboard
{
    public function __construct(
        public array $rows
    ) {
    }

    public function toTelegramKeyboard(): TelegramKeyboard
    {
        $buttons = array_map(function (array $row) {
            return array_map(fn (KeyboardButton|array $button) => is_array($button) ? $button : $button->toTelegramButton(), $row);
        }, $this->rows);

        return TelegramKeyboard::make([
            'inline_keyboard' => $buttons,
            'resize_keyboard' => true,
        ]);
    }
}
