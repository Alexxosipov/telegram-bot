<?php

namespace Alexxosipov\TelegramBot\Response\Sender;

use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Response\Response;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;

class ResponseSender implements ResponseSenderContract
{
    public function send(TelegramUser $telegramUser, ?Response $response): void
    {
        if (is_null($response)) {
            return;
        }

        $message = [
            'chat_id' => $telegramUser->id,
            'text' => $response->text,
            'parse_mode' => 'HTML',
            'disable_notification' => true,
            "disable_web_page_preview" => true,
        ];

        if ($response->keyboard) {
            $message['reply_markup'] = $response->keyboard->toTelegramKeyboard();
        }

        if ( ! $telegramUser->message_id) {
            $message = Telegram::sendMessage($message);
            $telegramUser->update(['message_id' => $message->messageId]);

            return;
        }

        $data = array_merge($message, [
            'message_id' => $telegramUser->message_id,
        ]);

        try {
            Telegram::editMessageText($data);
        } catch (TelegramResponseException $e) {
            // the only way to determine error when message is not modified
            if ($e->getMessage() === 'Bad Request: message is not modified: specified new message content and reply markup are exactly the same as a current content and reply markup of the message') {
                return;
            }

            $message = Telegram::sendMessage($message);
            $telegramUser->update(['message_id' => $message->messageId]);
            report($e);
        }
    }

    public function deleteMessage(TelegramUser $telegramUser, int $messageId): void
    {
        if ( ! $messageId) {
            return;
        }

        try {
            Telegram::deleteMessage([
                'chat_id' => $telegramUser->id,
                'message_id' => $messageId,
            ]);
        } catch (TelegramResponseException) {
        }
    }

    public function answerCallbackQuery(array $data): void
    {
        Telegram::answerCallbackQuery($data);
    }

    public function sendRaw(array $data): void
    {
        Telegram::sendMessage($data);
    }

    public function setWebhook(array $data): void
    {
        Telegram::setWebhook($data);
    }
}