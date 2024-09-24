<?php

namespace Alexxosipov\Telegram\Traits;

use Alexxosipov\Telegram\Models\TelegramUser;
use Telegram\Bot\Objects\Update;

trait RecognizesUser
{
    public function getUserFromUpdate(Update $update): ?TelegramUser
    {
        if ($update->message) {
            return $this->getTelegramUser(
                $update->message->from->id,
                $update->message->from->firstName,
                $update->message->from->lastName,
                $update->message->from->username,
            );
        }

        if ($update->callbackQuery) {
            return $this->getTelegramUser(
                $update->callbackQuery->from->id,
                $update->callbackQuery->from->firstName,
                $update->callbackQuery->from->lastName,
                $update->callbackQuery->from->username,
            );
        }

        return null;
    }

    private function getTelegramUser(
        int $id,
        ?string $firstName,
        ?string $lastName,
        ?string $userName,
    ): TelegramUser
    {
        return TelegramUser::firstOrCreate([
            'id' => $id,
        ], [
            'username' => $userName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'action' => config('telegram-bot.default-action'),
        ]);
    }
}