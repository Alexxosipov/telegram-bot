<?php

declare(strict_types=1);

namespace Alexxosipov\TelegramBot\Storage;

use Alexxosipov\TelegramBot\Models\TelegramUser;

abstract readonly class BaseStorage implements StorageContract
{
    public function __construct(
        protected TelegramUser $user,
    ) {
    }

    abstract public function set(string $key, int|string $value): void;

    abstract public function get(string $key): int|string|null;

    abstract public function delete(string $key): void;
}
