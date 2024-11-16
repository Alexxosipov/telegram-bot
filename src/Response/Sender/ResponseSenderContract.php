<?php

namespace Alexxosipov\TelegramBot\Response\Sender;

use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Response\Response;

interface ResponseSenderContract
{
    public function send(TelegramUser $telegramUser, ?Response $response): ?int;

    public function deleteMessage(TelegramUser $telegramUser, int $messageId): void;

    public function answerCallbackQuery(array $data): void;

    public function sendRaw(array $data): void;

    public function setWebhook(array $data): void;
}