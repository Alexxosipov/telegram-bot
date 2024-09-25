<?php

declare(strict_types=1);

namespace Alexxosipov\TelegramBot\Response;

use App\Enums\Telegram\TelegramAction;
use BackedEnum;
use Telegram\Bot\Keyboard\Button;
use Telegram\Bot\Keyboard\Keyboard;

readonly class KeyboardButton
{
    public function __construct(
        public string      $label,
        public ?BackedEnum $action = null,
        public ?array      $data = null,
        public ?string     $url = null,
    ) {
    }

    public function toTelegramButton(): Button
    {
        if ($this->url) {
            return Keyboard::inlineButton([
                'text' => $this->label,
                'url' => $this->url,
            ]);
        }

        $data = ['action' => $this->action->value];

        if ($this->data) {
            $data = array_merge($data, $this->data);
        }

        return Keyboard::inlineButton([
            'text' => $this->label,
            'callback_data' => json_encode($data),
        ]);
    }
}
