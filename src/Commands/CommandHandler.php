<?php

declare(strict_types=1);

namespace Alexxosipov\TelegramBot\Commands;

use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Response\Response;
use Alexxosipov\TelegramBot\Storage\StorageContract;
use Alexxosipov\TelegramBot\TelegramBot;
use Alexxosipov\TelegramBot\Traits\InteractsWithTelegramUser;

abstract class CommandHandler
{
    use InteractsWithTelegramUser;

    public function __construct(
        protected TelegramUser $telegramUser,
        protected TelegramBot  $telegramBot,
        protected StorageContract  $storage,
        protected ?string      $params = null
    ) {}

    abstract public function handle(): ?Response;
}
