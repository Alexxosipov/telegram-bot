<?php

declare(strict_types=1);

namespace Alexxosipov\Telegram\Storage;

use Alexxosipov\Telegram\Models\TelegramStorage;

readonly class DatabaseStorage extends BaseStorage
{
    public function set(string $key, int|string $value): void
    {
        TelegramStorage::query()
            ->upsert(
                [
                    'telegram_user_id' => $this->user->id,
                    'key' => $key,
                    'value' => $value,
                ],
                ['telegram_user_id', 'key'],
                ['value']
            );
    }

    public function get(string $key): int|string|null
    {
        return TelegramStorage::query()
            ->where('telegram_user_id', $this->user->id)
            ->where('key', $key)
            ->value('value');
    }

    public function delete(string $key): void
    {
        TelegramStorage::query()
            ->where('telegram_user_id', $this->user->id)
            ->where('key', $key)
            ->delete();
    }
}
