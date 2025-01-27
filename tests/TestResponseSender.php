<?php

namespace Alexxosipov\TelegramBot\Tests;

use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Response\Response;
use Alexxosipov\TelegramBot\Response\Sender\ResponseSenderContract;

class TestResponseSender implements ResponseSenderContract
{
    public function send(TelegramUser $telegramUser, ?Response $response): ?int
    {
    }

    public function deleteMessage(TelegramUser $telegramUser, int $messageId): void
    {
    }

    public function answerCallbackQuery(array $data): void
    {
        // TODO: Implement answerCallbackQuery() method.
    }

    public function sendRaw(array $data): void
    {
        // TODO: Implement sendRaw() method.
    }

    public function setWebhook(array $data): void
    {
        // TODO: Implement setWebhook() method.
    }
}