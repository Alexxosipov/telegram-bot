<?php

namespace Alexxosipov\Telegram\Actions;

use Alexxosipov\Telegram\Exceptions\TelegramBotException;
use Alexxosipov\Telegram\Keyboards\Keyboard;
use Alexxosipov\Telegram\Models\TelegramUser;
use Alexxosipov\Telegram\Response\Response;
use Alexxosipov\Telegram\Storage\BaseStorage;
use Alexxosipov\Telegram\TelegramBot;
use Alexxosipov\Telegram\Traits\InteractsWithTelegramUser;

abstract class ActionHandler
{
    use InteractsWithTelegramUser;

    public function __construct(
        protected readonly TelegramUser   $telegramUser,
        protected readonly TelegramBot $telegramBot,
        protected readonly BaseStorage    $storage,
    ) {}

    public function buildPreviewMessage(array $data = []): Response
    {
        throw new TelegramBotException(static::class . '::buildPreviewMessage is not implemented');
    }

    protected function buildKeyboard(string $keyboard): Keyboard
    {
        return $keyboard::make($this->telegramUser)->build();
    }
}