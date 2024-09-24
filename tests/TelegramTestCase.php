<?php

namespace Tests;

use Alexxosipov\Telegram\Models\TelegramUser;
use Alexxosipov\Telegram\TelegramBot;
use Orchestra\Testbench\TestCase;
use Telegram\Bot\Objects\Update;

class TelegramTestCase extends TestCase
{
    public function sendTextMessage(
        int     $userId,
        string  $text,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $username = null,
    ): void
    {
        $bot = TelegramTestCase::getBot();

        $update = new Update([
            'update_id' => rand(1000, 1000000),
            'message' => [
                'message_id' => rand(1, 100),
                'from' => [
                    'id' => $userId,
                    'is_bot' => false,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'language_code' => 'en',
                ],
                'chat' => [
                    'id' => $userId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'type' => 'private',
                ],
                'date' => now()->timestamp,
                'text' => $text,
            ],
        ]);

        $bot->handle($update);
    }

    public function sendCallbackQuery(
        int         $userId,
        \BackedEnum $action,
        array       $data = [],
        ?string     $firstName = null,
        ?string     $lastName = null,
        ?string     $username = null,
    ): void
    {
        $bot = TelegramTestCase::getBot();

        $data['action'] = $action->value;

        $update = new Update([
            'update_id' => rand(10000, 10000000),
            'callback_query' => [
                'id' => rand(10000, 1000000),
                'from' => [
                    'id' => $userId,
                    'is_bot' => false,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'language_code' => 'en',
                ],

                'message' => [
                    'message_id' => rand(1, 100),
                    'chat' => [
                        'id' => rand(10000, 1000000),
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'username' => $username,
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