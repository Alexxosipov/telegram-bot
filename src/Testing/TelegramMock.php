<?php

namespace Alexxosipov\Telegram\Testing;

use Alexxosipov\Telegram\Models\TelegramUser;
use Alexxosipov\Telegram\TelegramBot;
use Telegram\Bot\Objects\Update;

class TelegramMock
{
    public static function sendTextMessage(TelegramUser $telegramUser, string $text): void
    {
        $bot = TelegramMock::getBot();

        $update = new Update([
            'update_id' => rand(1000, 1000000),
            'message' => [
                'message_id' => rand(1, 100),
                'from' => [
                    'id' => $telegramUser->id,
                    'is_bot' => false,
                    'first_name' => $telegramUser->first_name,
                    'last_name' => $telegramUser->last_name,
                    'username' => $telegramUser->username,
                    'language_code' => 'en',
                ],
                'chat' => [
                    'id' => $telegramUser->id,
                    'first_name' => $telegramUser->first_name,
                    'last_name' => $telegramUser->last_name,
                    'username' => $telegramUser->username,
                    'type' => 'private',
                ],
                'date' => now()->timestamp,
                'text' => $text,
            ],
        ]);

        $bot->handle($update);
    }

    public static function sendCallbackQuery(TelegramUser $telegramUser, \BackedEnum $action, array $data = []): void
    {
        $bot = TelegramMock::getBot();

        $data['action'] = $action->value;

        $update = new Update([
            'update_id' => rand(10000, 10000000),
            'callback_query' => [
                'id' => rand(10000, 1000000),
                'from' => [
                    'id' => $telegramUser->id,
                    'is_bot' => false,
                    'first_name' => $telegramUser->first_name,
                    'last_name' => $telegramUser->last_name,
                    'username' => $telegramUser->username,
                    'language_code' => 'en',
                ],

                'message' => [
                    'message_id' => rand(1, 100),
                    'chat' => [
                        'id' => rand(10000, 1000000),
                        'first_name' => $telegramUser->first_name,
                        'last_name' => $telegramUser->last_name,
                        'username' => $telegramUser->username,
                        'type' => 'private',
                    ],
                    'date' => now()->timestamp,
                    'text' => 'some text.',
                ],
                'chat_instance' => '1234567890abcdef',
                'data' => json_encode($data),
            ],
        ]);

        $bot->handle($update);
    }

    private static function getBot(): TelegramBot
    {
        return app(TelegramBot::class);
    }
}