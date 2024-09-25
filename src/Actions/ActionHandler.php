<?php

namespace Alexxosipov\TelegramBot\Actions;

use Alexxosipov\TelegramBot\Actions\Contracts\HasCallbackQuery;
use Alexxosipov\TelegramBot\Example\Enums\Action;
use Alexxosipov\TelegramBot\Exceptions\TelegramBotException;
use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Response\Keyboard;
use Alexxosipov\TelegramBot\Response\Response;
use Alexxosipov\TelegramBot\Storage\StorageContract;
use Alexxosipov\TelegramBot\TelegramBot;
use Alexxosipov\TelegramBot\Traits\InteractsWithTelegramUser;

abstract class ActionHandler implements HasCallbackQuery
{
    use InteractsWithTelegramUser;

    public function __construct(
        protected readonly TelegramUser $telegramUser,
        protected readonly TelegramBot  $telegramBot,
        protected readonly StorageContract  $storage,
    ) {}

    public function handleCallbackQuery(Action $action, array $data = []): ?Response
    {
        return $this->sendNextActionPreview($action, $data);
    }

    public function buildPreviewMessage(array $data = []): Response
    {
        throw new TelegramBotException(static::class . '::buildPreviewMessage is not implemented');
    }

    protected function buildKeyboard(string $keyboard): Keyboard
    {
        return $keyboard::make($this->telegramUser)->build();
    }

    public function authorize(): bool
    {
        return true;
    }
}