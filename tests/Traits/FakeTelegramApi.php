<?php

namespace Tests\Traits;

use Alexxosipov\TelegramBot\Response\Response;
use Alexxosipov\TelegramBot\Response\Sender\ResponseSenderContract;
use Alexxosipov\TelegramBot\TelegramBot;
use Telegram\Bot\Objects\Update;
use Tests\TelegramTestCase;
use Tests\TestResponseSender;

trait FakeTelegramApi
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(ResponseSenderContract::class, TestResponseSender::class);
    }

    public function sendTextMessage(
        int     $userId,
        string  $text,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $username = null,
        bool $isCommand = false
    ): ?Response
    {
        $bot = TelegramTestCase::getBot();

        $update = [
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
        ];

        if ($isCommand) {
            $update['message']['entities'][] = [
                'type' => 'bot_command',
                'offset' => 0,
                'length' => 1
            ];
        }

        $update = new Update($update);

        return $bot->handle($update);
    }

    public function sendCallbackQuery(
        int         $userId,
        \BackedEnum $action,
        array       $data = [],
        ?string     $firstName = null,
        ?string     $lastName = null,
        ?string     $username = null,
    ): ?Response
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

        return $bot->handle($update);
    }

    private static function getBot(): TelegramBot
    {
        return app(TelegramBot::class);
    }
}