<?php

namespace Alexxosipov\TelegramBot\Storage;

interface StorageContract
{
    public function set(string $key, int|string $value): void;

    public function get(string $key): int|string|null;

    public function delete(string $key): void;
}