<?php

namespace Alexxosipov\Telegram\Traits;

use Alexxosipov\Telegram\Response\Response;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;

trait SendsResponse
{
    public function sendResponse(?Response $response): void
    {
        if (is_null($response)) {
            return;
        }

        $message = [
            'chat_id' => $this->telegramUser->id,
            'text' => $response->text,
            'parse_mode' => 'HTML',
            'disable_notification' => true,
            "disable_web_page_preview" => true,
        ];

        if ($response->keyboard) {
            $message['reply_markup'] = $response->keyboard->toTelegramKeyboard();
        }

        if ( ! $this->telegramUser->message_id) {
            $message = Telegram::sendMessage($message);
            $this->telegramUser->update(['message_id' => $message->messageId]);

            return;
        }

        $data = array_merge($message, [
            'message_id' => $this->telegramUser->message_id,
        ]);

        try {
            Telegram::editMessageText($data);
        } catch (TelegramResponseException $e) {
            // the only way to determine error when message is not modified
            if ($e->getMessage() === 'Bad Request: message is not modified: specified new message content and reply markup are exactly the same as a current content and reply markup of the message') {
                return;
            }

            $message = Telegram::sendMessage($message);
            $this->telegramUser->update(['message_id' => $message->messageId]);
            Log::debug('Exception data', [
                $e->getErrorType(), $e->getMessage(), $e->getCode(), $e->getFile(), $e->getLine(),
            ]);
            report($e);
        }
    }
}