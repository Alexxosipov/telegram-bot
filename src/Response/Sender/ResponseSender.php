<?php

namespace Alexxosipov\TelegramBot\Response\Sender;

use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Response\Response;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;

class ResponseSender implements ResponseSenderContract
{
    private Api $telegram;

    public function __construct() {
        $this->telegram = new Api(config('telegram-bot.bot-token'));
    }

    public function send(TelegramUser $telegramUser, ?Response $response): ?int
    {
        if (is_null($response)) {
            return null;
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

        if ( ! $telegramUser->message_id || $response->shouldSendNewMessage) {
            $message = $this->telegram->sendMessage($message);

            if ($response->shouldUpdateDbMessageId) {
                $telegramUser->update(['message_id' => $message->messageId]);
            }

            return $message->messageId;
        }

        $data = array_merge($message, [
            'message_id' => $telegramUser->message_id,
        ]);

        try {
            $this->telegram->editMessageText($data);
        } catch (TelegramResponseException $e) {
            // the only way to determine error when message is not modified
            if ($e->getMessage() === 'Bad Request: message is not modified: specified new message content and reply markup are exactly the same as a current content and reply markup of the message') {
                return null;
            }

            $message = $this->telegram->sendMessage($message);
            $telegramUser->update(['message_id' => $message->messageId]);
            report($e);
        }

        return $telegramUser->message_id;
    }

    public function deleteMessage(TelegramUser $telegramUser, int $messageId): void
    {
        if ( ! $messageId) {
            return;
        }

        try {
            $this->telegram->deleteMessage([
                'chat_id' => $telegramUser->id,
                'message_id' => $messageId,
            ]);
        } catch (TelegramResponseException) {
        }
    }

    public function answerCallbackQuery(array $data): void
    {
        $this->telegram->answerCallbackQuery($data);
    }

    public function sendRaw(array $data): void
    {
        $this->telegram->sendMessage($data);
    }

    public function setWebhook(array $data): void
    {
        $this->telegram->setWebhook($data);
    }
}
