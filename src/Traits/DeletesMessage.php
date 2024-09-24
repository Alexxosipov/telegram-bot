<?php

namespace Alexxosipov\Telegram\Traits;

use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;

trait DeletesMessage
{
    public function deleteMessage(int|null $messageId): void
    {
        if ( ! $messageId) {
            return;
        }

        try {
            Telegram::deleteMessage([
                'chat_id' => $this->telegramUser->id,
                'message_id' => $messageId,
            ]);
        } catch (TelegramResponseException) {
        }
    }
}