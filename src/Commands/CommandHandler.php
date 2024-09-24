<?php

declare(strict_types=1);

namespace Alexxosipov\Telegram\Commands;

use Alexxosipov\Telegram\Models\TelegramUser;
use Alexxosipov\Telegram\Response\Response;
use Alexxosipov\Telegram\Storage\BaseStorage;
use Alexxosipov\Telegram\TelegramBot;
use Alexxosipov\Telegram\Traits\InteractsWithTelegramUser;

abstract class CommandHandler
{
    use InteractsWithTelegramUser;

    public function __construct(
        protected TelegramUser   $telegramUser,
        protected TelegramBot $telegramBot,
        protected BaseStorage    $storage,
        protected ?string        $params = null
    ) {}

    abstract public function handle(): ?Response;
}
