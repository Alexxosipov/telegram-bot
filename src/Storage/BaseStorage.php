<?php

declare(strict_types=1);

namespace Alexxosipov\Telegram\Storage;

use Alexxosipov\Telegram\Models\TelegramUser;

abstract readonly class BaseStorage
{
    public function __construct(
        protected TelegramUser $user,
    ) {
    }

    abstract public function set(string $key, int|string $value): void;

    abstract public function get(string $key): int|string|null;

    abstract public function delete(string $key): void;
}
